<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Jobs\ConvertEpisodeToHlsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EpisodeController extends Controller
{
    /**
     * Get HLS conversion status for an episode
     */
    public function getHlsStatus(Episode $episode)
    {
        return response()->json([
            'status' => $episode->hls_status,
            'progress' => $episode->hls_progress,
            'message' => $this->getHlsStatusMessage($episode),
            'error_message' => $episode->hls_error_message,
            'processed_at' => $episode->hls_processed_at
        ]);
    }

    /**
     * Retry HLS conversion for an episode
     */
    public function retryHlsConversion(Episode $episode)
    {
        try {
            // Reset episode HLS status
            $episode->update([
                'hls_status' => 'pending',
                'hls_progress' => 0,
                'hls_error_message' => null,
                'hls_processed_at' => null
            ]);

            // Dispatch HLS conversion job
            ConvertEpisodeToHlsJob::dispatch($episode);

            return response()->json([
                'success' => true,
                'message' => 'HLS conversion restarted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to restart HLS conversion for episode', [
                'episode_id' => $episode->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to restart HLS conversion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload episode video file
     */
    public function uploadVideo(Request $request, Episode $episode)
    {
        try {
            // Handle Resumable.js chunk upload
            if ($request->has('resumableChunkNumber')) {
                return $this->handleResumableUpload($request, $episode);
            }
            
            // Handle regular file upload (fallback)
            $request->validate([
                'video' => 'required|file|mimes:mp4,avi,mkv,mov,wmv,flv,webm|max:2048000' // 2GB max
            ]);

            $file = $request->file('video');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store file in storage/app/videos/episodes/
            $path = $file->storeAs('videos/episodes', $filename);

            // Update episode with file path
            $episode->update([
                'file' => $path,
                'hls_status' => 'pending',
                'hls_progress' => 0,
                'hls_error_message' => null,
                'hls_processed_at' => null
            ]);

            // Dispatch HLS conversion job
            ConvertEpisodeToHlsJob::dispatch($episode);

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully and HLS conversion started',
                'filename' => $filename,
                'path' => $path
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to upload episode video', [
                'episode_id' => $episode->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to upload video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Resumable.js chunk upload
     */
    private function handleResumableUpload(Request $request, Episode $episode)
    {
        try {
            // Get chunk parameters
            $chunkNumber = $request->input('resumableChunkNumber');
            $totalChunks = $request->input('resumableTotalChunks');
            $chunkSize = $request->input('resumableChunkSize');
            $totalSize = $request->input('resumableTotalSize');
            $identifier = $request->input('resumableIdentifier');
            $filename = $request->input('resumableFilename');
            $file = $request->file('file');

            if (!$file) {
                return response()->json(['error' => 'No file uploaded'], 400);
            }

            // Validate file type and size on first chunk
            if ($chunkNumber == 1) {
                $this->validateFile($file, $filename, $totalSize);
            }

            // Create temporary directory for chunks
            $tempDir = storage_path('app/temp/resumable/' . $identifier);
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Save chunk with proper padding for consistent ordering
            $chunkPath = $tempDir . '/chunk_' . str_pad($chunkNumber, 6, '0', STR_PAD_LEFT) . '.part';
            move_uploaded_file($file->getPathname(), $chunkPath);

            // Log progress
            Log::info('Chunk uploaded', [
                'episode_id' => $episode->id,
                'chunk_number' => $chunkNumber,
                'total_chunks' => $totalChunks,
                'identifier' => $identifier
            ]);

            // Check if all chunks are uploaded
            $uploadedChunks = glob($tempDir . '/chunk_*.part');
            if (count($uploadedChunks) == $totalChunks) {
                Log::info('All chunks uploaded, starting merge', [
                    'episode_id' => $episode->id,
                    'total_chunks' => $totalChunks,
                    'filename' => $filename
                ]);

                // Merge chunks into final file
                $finalFilename = time() . '_' . $this->sanitizeFilename($filename);
                $finalPath = storage_path('app/videos/episodes/' . $finalFilename);
                
                // Ensure videos/episodes directory exists
                $videosDir = storage_path('app/videos/episodes');
                if (!file_exists($videosDir)) {
                    mkdir($videosDir, 0755, true);
                }

                // Merge chunks in correct order with enhanced error handling
                Log::info('Starting episode chunk merge process', [
                    'episode_id' => $episode->id,
                    'temp_dir' => $tempDir,
                    'final_path' => $finalPath,
                    'total_chunks' => $totalChunks,
                    'chunk_size' => $chunkSize
                ]);

                // Check available disk space
                $freeSpace = disk_free_space($videosDir);
                if ($freeSpace < ($totalSize * 2)) {
                    Log::error('Insufficient disk space for episode merge', [
                        'required' => $totalSize * 2,
                        'available' => $freeSpace,
                        'directory' => $videosDir
                    ]);
                    $this->cleanUpChunks($tempDir);
                    throw new \Exception("Insufficient disk space for file merge.");
                }

                // Check if directory is writable
                if (!is_writable($videosDir)) {
                    Log::error('Episode videos directory is not writable', ['directory' => $videosDir]);
                    $this->cleanUpChunks($tempDir);
                    throw new \Exception("Videos directory is not writable.");
                }

                $finalFile = fopen($finalPath, 'wb');
                if (!$finalFile) {
                    Log::error('Failed to open final episode file', ['file_path' => $finalPath]);
                    $this->cleanUpChunks($tempDir);
                    throw new \Exception("Failed to create final file.");
                }

                $totalBytesWritten = 0;
                for ($i = 1; $i <= $totalChunks; $i++) {
                    $chunkFile = $tempDir . '/chunk_' . str_pad($i, 6, '0', STR_PAD_LEFT) . '.part';
                    
                    Log::info('Processing episode chunk', [
                        'episode_id' => $episode->id,
                        'chunk_number' => $i,
                        'chunk_file' => $chunkFile
                    ]);
                    
                    if (file_exists($chunkFile)) {
                        $chunkData = file_get_contents($chunkFile);
                        if ($chunkData === false) {
                            Log::error('Failed to read episode chunk data', [
                                'episode_id' => $episode->id,
                                'chunk_number' => $i,
                                'chunk_file' => $chunkFile
                            ]);
                            fclose($finalFile);
                            if (file_exists($finalPath)) {
                                unlink($finalPath);
                            }
                            $this->cleanUpChunks($tempDir);
                            throw new \Exception("Failed to read chunk {$i}");
                        }
                        
                        $bytesWritten = fwrite($finalFile, $chunkData);
                        if ($bytesWritten === false) {
                            Log::error('Failed to write episode chunk to final file', [
                                'episode_id' => $episode->id,
                                'chunk_number' => $i,
                                'chunk_size' => strlen($chunkData)
                            ]);
                            fclose($finalFile);
                            if (file_exists($finalPath)) {
                                unlink($finalPath);
                            }
                            $this->cleanUpChunks($tempDir);
                            throw new \Exception("Failed to write chunk {$i} to final file.");
                        }
                        
                        $totalBytesWritten += $bytesWritten;
                        Log::info('Episode chunk written successfully', [
                            'episode_id' => $episode->id,
                            'chunk_number' => $i,
                            'bytes_written' => $bytesWritten,
                            'total_bytes' => $totalBytesWritten
                        ]);
                    } else {
                        Log::error('Missing episode chunk file', [
                            'episode_id' => $episode->id,
                            'chunk_number' => $i,
                            'chunk_file' => $chunkFile,
                            'existing_chunks' => glob($tempDir . '/chunk_*.part')
                        ]);
                        fclose($finalFile);
                        if (file_exists($finalPath)) {
                            unlink($finalPath);
                        }
                        $this->cleanUpChunks($tempDir);
                        throw new \Exception("Missing chunk {$i}");
                    }
                }
                fclose($finalFile);

                Log::info('Episode file merge completed successfully', [
                    'episode_id' => $episode->id,
                    'file_path' => $finalPath,
                    'expected_size' => $totalSize,
                    'total_bytes_written' => $totalBytesWritten
                ]);

                // Set proper file permissions
                chmod($finalPath, 0644);

                // Verify final file size
                $finalFileSize = filesize($finalPath);
                if ($finalFileSize != $totalSize) {
                    unlink($finalPath);
                    $this->cleanUpChunks($tempDir);
                    throw new \Exception("File size mismatch. Expected: {$totalSize}, Got: {$finalFileSize}");
                }

                // Clean up chunks
                $this->cleanUpChunks($tempDir);

                // Update episode with file path
                $relativePath = 'videos/episodes/' . $finalFilename;
                $episode->update([
                    'file' => $relativePath,
                    'hls_status' => 'pending',
                    'hls_progress' => 0,
                    'hls_error_message' => null,
                    'hls_processed_at' => null
                ]);

                Log::info('File merged successfully, starting HLS conversion', [
                    'episode_id' => $episode->id,
                    'file_path' => $relativePath,
                    'file_size' => $finalFileSize
                ]);

                // Dispatch HLS conversion job
                ConvertEpisodeToHlsJob::dispatch($episode);

                return response()->json([
                    'success' => true,
                    'message' => 'Video uploaded successfully and HLS conversion started',
                    'filename' => $finalFilename,
                    'path' => $relativePath,
                    'size' => $finalFileSize
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Chunk uploaded successfully']);

        } catch (\Exception $e) {
            Log::error('Failed to handle resumable upload', [
                'episode_id' => $episode->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate file type and size
     */
    private function validateFile($file, $filename, $totalSize)
    {
        // Check file size (max 2GB)
        $maxSize = 2 * 1024 * 1024 * 1024; // 2GB in bytes
        if ($totalSize > $maxSize) {
            abort(413, 'File size too large. Maximum allowed size is 2GB.');
        }

        // Check file extension
        $allowedExtensions = ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'webm', 'm4v', '3gp'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            abort(422, 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions));
        }

        // Check MIME type
        $allowedMimeTypes = [
            'video/mp4', 'video/avi', 'video/x-matroska', 'video/quicktime',
            'video/x-ms-wmv', 'video/x-flv', 'video/webm', 'video/x-m4v',
            'video/3gpp', 'video/3gpp2'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            abort(422, 'File MIME type not allowed.');
        }
    }

    /**
     * Sanitize filename
     */
    private function sanitizeFilename($filename)
    {
        // Remove any directory traversal attempts
        $filename = basename($filename);
        
        // Replace spaces and special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'video_' . time() . '.mp4';
        }
        
        return $filename;
    }

    /**
     * Clean up temporary chunk files
     */
    private function cleanUpChunks($tempDir)
    {
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($tempDir);
        }
    }

    /**
     * Check if chunk already exists (for Resumable.js)
     */
    public function checkChunk(Request $request, Episode $episode)
    {
        try {
            $identifier = $request->input('resumableIdentifier');
            $chunkNumber = $request->input('resumableChunkNumber');
            $chunkPath = storage_path('app/temp/resumable/' . $identifier . '/' . $chunkNumber);
            
            if (file_exists($chunkPath)) {
                return response()->json(['success' => true, 'message' => 'Chunk exists']);
            }
            
            return response()->json(['success' => false, 'message' => 'Chunk not found'], 404);
            
        } catch (\Exception $e) {
            Log::error('Failed to check chunk', [
                'episode_id' => $episode->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['success' => false, 'error' => 'Failed to check chunk'], 500);
        }
    }

    /**
     * Get user-friendly status message
     */
    private function getHlsStatusMessage(Episode $episode): string
    {
        switch ($episode->hls_status) {
            case 'pending':
                return 'Waiting to start conversion...';
            case 'processing':
                return 'Converting to HLS format...';
            case 'completed':
                return 'Conversion completed successfully';
            case 'failed':
                return 'Conversion failed';
            default:
                return 'Unknown status';
        }
    }
}

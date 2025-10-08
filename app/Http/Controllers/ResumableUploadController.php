<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Movie;
use App\Jobs\ConvertVideoToHlsJob;

class ResumableUploadController extends Controller
{
    /**
     * Handle Resumable.js upload requests
     */
    public function upload(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'resumableIdentifier' => 'required|string',
            'resumableFilename' => 'required|string',
            'resumableTotalSize' => 'required|integer|min:1',
            'resumableTotalChunks' => 'required|integer|min:1',
            'resumableChunkNumber' => 'required|integer|min:1',
            'resumableChunkSize' => 'required|integer|min:1',
            'resumableCurrentChunkSize' => 'required|integer|min:1',
            'file' => 'required|file',
            'movie_id' => 'required|string|exists:movie,id'
        ]);

        if ($validator->fails()) {
            Log::error('Resumable upload validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Invalid request',
                'details' => $validator->errors()->toArray()
            ], 400);
        }

        $identifier = $request->input('resumableIdentifier');
        $filename = $request->input('resumableFilename');
        $totalChunks = $request->input('resumableTotalChunks');
        $chunkNumber = $request->input('resumableChunkNumber');
        $chunkSize = $request->input('resumableChunkSize');
        $totalSize = $request->input('resumableTotalSize');
        $movieId = $request->input('movie_id');

        // Create upload directory if it doesn't exist
        $uploadDir = 'uploads/resumable/' . $identifier;
        $tempDir = 'temp/resumable/' . $identifier;

        // Check if this is the first chunk
        if ($chunkNumber == 1) {
            // Validate file type and size
            $this->validateFile($request->file('file'), $filename, $totalSize);
        }

        // Store the chunk
        $chunk = $request->file('file');
        $chunkPath = $tempDir . '/chunk_' . str_pad($chunkNumber, 6, '0', STR_PAD_LEFT) . '.part';
        
        Storage::disk('local')->put($chunkPath, file_get_contents($chunk->getPathname()));

        // Log chunk upload progress
        Log::info('Chunk uploaded', [
            'movie_id' => $movieId,
            'chunk_number' => $chunkNumber,
            'total_chunks' => $totalChunks,
            'identifier' => $identifier
        ]);

        // Check if all chunks have been uploaded
        $uploadedChunks = Storage::disk('local')->files($tempDir);
        
        if (count($uploadedChunks) == $totalChunks) {
            Log::info('All chunks uploaded, starting merge', [
                'movie_id' => $movieId,
                'total_chunks' => $totalChunks,
                'filename' => $filename
            ]);

            // Merge chunks into final file
            $finalPath = $uploadDir . '/' . $this->sanitizeFilename($filename);
            $this->mergeChunks($tempDir, $finalPath, $totalChunks, $chunkSize);
            
            // Clean up temp directory
            Storage::disk('local')->deleteDirectory($tempDir);

            Log::info('File merged successfully, starting HLS conversion', [
                'movie_id' => $movieId,
                'file_path' => $finalPath,
                'file_size' => $totalSize
            ]);
            
            // Get movie and trigger HLS conversion
            $movie = Movie::find($movieId);
            if ($movie) {
                // Initialize HLS conversion status
                $movie->initializeHlsConversion();
                
                // Get the full file path for conversion
                $fullFilePath = Storage::disk('local')->path($finalPath);
                
                // Dispatch HLS conversion job
                ConvertVideoToHlsJob::dispatch($fullFilePath, $movie);
            }
            
            // Return success response with file info
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'path' => $finalPath,
                'size' => $totalSize,
                'url' => Storage::url($finalPath),
                'movie_id' => $movieId,
                'hls_conversion_started' => $movie ? true : false
            ]);
        }

        // Return success for this chunk
        return response()->json(['success' => true]);
    }

    /**
     * Check if chunk already exists
     */
    public function checkChunk(Request $request)
    {
        $identifier = $request->input('resumableIdentifier');
        $chunkNumber = $request->input('resumableChunkNumber');
        $chunkSize = $request->input('resumableChunkSize');
        
        $chunkPath = 'temp/resumable/' . $identifier . '/chunk_' . str_pad($chunkNumber, 6, '0', STR_PAD_LEFT) . '.part';
        
        if (Storage::disk('local')->exists($chunkPath)) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['success' => false], 204);
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
     * Merge chunks into final file
     */
    private function mergeChunks($tempDir, $finalPath, $totalChunks, $chunkSize)
    {
        try {
            Log::info('Starting chunk merge process', [
                'temp_dir' => $tempDir,
                'final_path' => $finalPath,
                'total_chunks' => $totalChunks,
                'chunk_size' => $chunkSize
            ]);

            $finalFilePath = Storage::disk('local')->path($finalPath);
            $finalDir = dirname($finalFilePath);
            
            // Ensure directory exists with proper permissions
            if (!is_dir($finalDir)) {
                if (!mkdir($finalDir, 0755, true)) {
                    Log::error('Failed to create directory', ['directory' => $finalDir]);
                    abort(500, 'Failed to create upload directory.');
                }
                Log::info('Created directory', ['directory' => $finalDir]);
            }

            // Check if directory is writable
            if (!is_writable($finalDir)) {
                Log::error('Directory is not writable', ['directory' => $finalDir]);
                abort(500, 'Upload directory is not writable.');
            }

            // Check available disk space (at least 2x the expected file size)
            $expectedSize = $totalChunks * $chunkSize;
            $freeSpace = disk_free_space($finalDir);
            if ($freeSpace < ($expectedSize * 2)) {
                Log::error('Insufficient disk space', [
                    'required' => $expectedSize * 2,
                    'available' => $freeSpace,
                    'directory' => $finalDir
                ]);
                abort(500, 'Insufficient disk space for file merge.');
            }

            Log::info('Opening final file for writing', ['file_path' => $finalFilePath]);
            $finalFile = fopen($finalFilePath, 'wb');
            
            if (!$finalFile) {
                Log::error('Failed to open final file', ['file_path' => $finalFilePath]);
                abort(500, 'Failed to create final file.');
            }

            $totalBytesWritten = 0;
            
            for ($i = 1; $i <= $totalChunks; $i++) {
                $chunkPath = $tempDir . '/chunk_' . str_pad($i, 6, '0', STR_PAD_LEFT) . '.part';
                $chunkFilePath = Storage::disk('local')->path($chunkPath);
                
                Log::info('Processing chunk', [
                    'chunk_number' => $i,
                    'chunk_path' => $chunkPath,
                    'chunk_file_path' => $chunkFilePath
                ]);
                
                if (file_exists($chunkFilePath)) {
                    $chunkData = file_get_contents($chunkFilePath);
                    if ($chunkData === false) {
                        Log::error('Failed to read chunk data', ['chunk_file_path' => $chunkFilePath]);
                        fclose($finalFile);
                        unlink($finalFilePath);
                        abort(500, 'Failed to read chunk ' . $i . '.');
                    }
                    
                    $bytesWritten = fwrite($finalFile, $chunkData);
                    if ($bytesWritten === false) {
                        Log::error('Failed to write chunk to final file', [
                            'chunk_number' => $i,
                            'chunk_size' => strlen($chunkData)
                        ]);
                        fclose($finalFile);
                        unlink($finalFilePath);
                        abort(500, 'Failed to write chunk ' . $i . ' to final file.');
                    }
                    
                    $totalBytesWritten += $bytesWritten;
                    Log::info('Chunk written successfully', [
                        'chunk_number' => $i,
                        'bytes_written' => $bytesWritten,
                        'total_bytes' => $totalBytesWritten
                    ]);
                } else {
                    Log::error('Missing chunk file', [
                        'chunk_number' => $i,
                        'chunk_file_path' => $chunkFilePath,
                        'existing_chunks' => Storage::disk('local')->files($tempDir)
                    ]);
                    fclose($finalFile);
                    if (file_exists($finalFilePath)) {
                        unlink($finalFilePath);
                    }
                    abort(500, 'Missing chunk ' . $i . '. Upload failed.');
                }
            }
            
            fclose($finalFile);
            
            // Verify the merged file
            if (!file_exists($finalFilePath)) {
                Log::error('Final file was not created', ['file_path' => $finalFilePath]);
                abort(500, 'Failed to create merged file.');
            }
            
            $actualSize = filesize($finalFilePath);
            Log::info('File merge completed successfully', [
                'file_path' => $finalFilePath,
                'expected_size' => $expectedSize,
                'actual_size' => $actualSize,
                'total_bytes_written' => $totalBytesWritten
            ]);
            
            // Set proper file permissions
            chmod($finalFilePath, 0644);
            
        } catch (\Exception $e) {
            Log::error('Merge process failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'temp_dir' => $tempDir,
                'final_path' => $finalPath
            ]);
            
            // Clean up partial file if it exists
            if (isset($finalFilePath) && file_exists($finalFilePath)) {
                unlink($finalFilePath);
            }
            
            abort(500, 'File merge failed: ' . $e->getMessage());
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
     * Test endpoint to verify Resumable.js is working
     */
    public function test()
    {
        return response()->json([
            'status' => 'Resumable.js upload endpoint is working',
            'timestamp' => now()->toISOString(),
            'max_file_size' => '2GB',
            'allowed_types' => ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'webm', 'm4v', '3gp']
        ]);
    }
}

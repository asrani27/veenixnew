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

            // Create temporary directory for chunks
            $tempDir = storage_path('app/temp/resumable/' . $identifier);
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Save chunk
            $chunkPath = $tempDir . '/' . $chunkNumber;
            move_uploaded_file($file->getPathname(), $chunkPath);

            // Check if all chunks are uploaded
            $uploadedChunks = glob($tempDir . '/*');
            if (count($uploadedChunks) == $totalChunks) {
                // Merge chunks into final file
                $finalFilename = time() . '_' . $filename;
                $finalPath = storage_path('app/videos/episodes/' . $finalFilename);
                
                // Ensure videos/episodes directory exists
                $videosDir = storage_path('app/videos/episodes');
                if (!file_exists($videosDir)) {
                    mkdir($videosDir, 0755, true);
                }

                // Merge chunks
                $finalFile = fopen($finalPath, 'wb');
                for ($i = 1; $i <= $totalChunks; $i++) {
                    $chunkFile = $tempDir . '/' . $i;
                    if (file_exists($chunkFile)) {
                        $chunkData = file_get_contents($chunkFile);
                        fwrite($finalFile, $chunkData);
                    }
                }
                fclose($finalFile);

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

                // Dispatch HLS conversion job
                ConvertEpisodeToHlsJob::dispatch($episode);

                return response()->json([
                    'success' => true,
                    'message' => 'Video uploaded successfully and HLS conversion started',
                    'filename' => $finalFilename,
                    'path' => $relativePath
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Chunk uploaded']);

        } catch (\Exception $e) {
            Log::error('Failed to handle resumable upload', [
                'episode_id' => $episode->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
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

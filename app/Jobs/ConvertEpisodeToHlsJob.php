<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\VideoConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ConvertEpisodeToHlsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout
    public $tries = 2;
    public $backoff = [60, 300]; // 1 minute, then 5 minutes

    protected $episode;

    /**
     * Create a new job instance.
     */
    public function __construct(Episode $episode)
    {
        $this->episode = $episode;
    }

    /**
     * Execute the job.
     */
    public function handle(VideoConversionService $conversionService): void
    {
        try {
            Log::info('Starting HLS conversion for episode', [
                'episode_id' => $this->episode->id,
                'episode_number' => $this->episode->episode_number,
                'season_number' => $this->episode->season_number,
                'video_path' => $this->episode->file
            ]);

            // Update status to processing
            $this->episode->update([
                'hls_status' => 'processing',
                'hls_error_message' => null,
                'hls_progress' => 0
            ]);

            // Get TV show slug for folder structure
            $tvShow = $this->episode->tv;
            $tvSlug = $tvShow ? $tvShow->slug : 'unknown-tv';
            
            // Create folder structure: tv-slug/season-xx/episode-xx/
            $folderPath = sprintf('%s/season-%02d/episode-%02d', 
                $tvSlug, 
                $this->episode->season_number, 
                $this->episode->episode_number
            );

            // Convert video to HLS and upload to Wasabi
            $uploadedFiles = $conversionService->convertToHlsAndUpload(
                storage_path('app/' . $this->episode->file),
                $folderPath,
                $this->episode->id
            );

            // Extract playlist and segment paths from uploaded files
            $playlistPath = null;
            $segmentPaths = [];
            
            foreach ($uploadedFiles as $file) {
                if ($file['type'] === 'playlist') {
                    $playlistPath = $file['wasabi_path'];
                } elseif ($file['type'] === 'segment') {
                    $segmentPaths[] = $file['wasabi_path'];
                }
            }

            // Update episode with HLS paths
            $this->episode->update([
                'hls_status' => 'completed',
                'master_playlist_path' => $playlistPath,
                'hls_playlist_path' => $playlistPath,
                'hls_segment_paths' => json_encode($segmentPaths),
                'hls_progress' => 100,
                'hls_processed_at' => now(),
                'hls_error_message' => null
            ]);

            // Delete local video file after successful upload to Wasabi
            $this->deleteLocalVideoFile();

            Log::info('HLS conversion completed successfully', [
                'episode_id' => $this->episode->id,
                'files_count' => count($uploadedFiles)
            ]);

        } catch (Exception $e) {
            Log::error('HLS conversion failed for episode', [
                'episode_id' => $this->episode->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update episode with error
            $this->episode->update([
                'hls_status' => 'failed',
                'hls_error_message' => $e->getMessage()
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Delete local video file after successful upload to Wasabi
     */
    private function deleteLocalVideoFile(): void
    {
        try {
            if ($this->episode->file) {
                $localPath = storage_path('app/' . $this->episode->file);
                
                // Check if file exists before deleting
                if (file_exists($localPath)) {
                    unlink($localPath);
                    
                    Log::info('Local video file deleted successfully', [
                        'episode_id' => $this->episode->id,
                        'file_path' => $this->episode->file,
                        'local_path' => $localPath
                    ]);
                } else {
                    Log::warning('Local video file not found for deletion', [
                        'episode_id' => $this->episode->id,
                        'file_path' => $this->episode->file,
                        'local_path' => $localPath
                    ]);
                }
                
                // Also delete from resumable uploads directory if it exists
                $this->deleteFromResumableUploads();
            }
        } catch (Exception $e) {
            Log::error('Failed to delete local video file', [
                'episode_id' => $this->episode->id,
                'file_path' => $this->episode->file,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception as this is cleanup operation
        }
    }

    /**
     * Delete file from resumable uploads directory
     */
    private function deleteFromResumableUploads(): void
    {
        try {
            // Extract filename from the episode file path
            $filename = basename($this->episode->file);
            $resumablePath = storage_path('app/privates/uploads/resumable/' . $filename);
            
            if (file_exists($resumablePath)) {
                unlink($resumablePath);
                
                Log::info('Resumable upload file deleted successfully', [
                    'episode_id' => $this->episode->id,
                    'filename' => $filename,
                    'resumable_path' => $resumablePath
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to delete resumable upload file', [
                'episode_id' => $this->episode->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception as this is cleanup operation
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('HLS conversion job failed permanently', [
            'episode_id' => $this->episode->id,
            'error' => $exception->getMessage()
        ]);

        // Update episode with failed status
        $this->episode->update([
            'hls_status' => 'failed',
            'hls_error_message' => 'Job failed: ' . $exception->getMessage()
        ]);
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\VideoConversionService;
use App\Models\Movie;

class ConvertVideoToHlsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600; // 1 hour

    /**
     * The video file path.
     *
     * @var string
     */
    protected $videoPath;

    /**
     * The movie instance.
     *
     * @var \App\Models\Movie
     */
    protected $movie;

    /**
     * Create a new job instance.
     *
     * @param string $videoPath
     * @param \App\Models\Movie $movie
     * @return void
     */
    public function __construct(string $videoPath, Movie $movie)
    {
        $this->videoPath = $videoPath;
        $this->movie = $movie;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Update status to processing
            $this->movie->update([
                'hls_status' => 'processing',
                'hls_error' => null
            ]);

            Log::info('Starting HLS conversion for movie', [
                'movie_id' => $this->movie->id,
                'movie_slug' => $this->movie->slug,
                'video_path' => $this->videoPath
            ]);

            $videoService = new VideoConversionService();
            
            // Convert video to HLS and upload to Wasabi
            $uploadedFiles = $videoService->convertToHlsAndUpload(
                $this->videoPath,
                $this->movie->slug,
                $this->movie->id
            );

            // Update movie with HLS information
            $this->updateMovieWithHlsInfo($uploadedFiles);

            // Delete local video file after successful upload to Wasabi
            $this->deleteLocalVideoFile();

            Log::info('HLS conversion completed successfully', [
                'movie_id' => $this->movie->id,
                'files_count' => count($uploadedFiles)
            ]);

        } catch (\Exception $e) {
            Log::error('HLS conversion failed', [
                'movie_id' => $this->movie->id,
                'error' => $e->getMessage()
            ]);

            // Update movie with error status
            $this->movie->update([
                'hls_status' => 'failed',
                'hls_error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Update movie with HLS information
     *
     * @param array $uploadedFiles
     * @return void
     */
    private function updateMovieWithHlsInfo(array $uploadedFiles): void
    {
        $mainPlaylist = null;

        foreach ($uploadedFiles as $file) {
            if ($file['type'] === 'playlist' && basename($file['wasabi_path']) === 'playlist.m3u8') {
                $mainPlaylist = $file;
                break;
            }
        }

        $this->movie->update([
            'hls_status' => 'completed',
            'hls_master_playlist_url' => $mainPlaylist['url'] ?? null,
            'hls_master_playlist_path' => $mainPlaylist['wasabi_path'] ?? null,
            'hls_files' => json_encode($uploadedFiles),
            'hls_processed_at' => now(),
            'hls_error' => null,
            'file' => $this->movie->slug . '/playlist.m3u8'
        ]);
    }

    /**
     * Delete local video file after successful upload to Wasabi
     */
    private function deleteLocalVideoFile(): void
    {
        try {
            if ($this->videoPath) {
                // Check if file exists before deleting
                if (file_exists($this->videoPath)) {
                    unlink($this->videoPath);
                    
                    Log::info('Local video file deleted successfully', [
                        'movie_id' => $this->movie->id,
                        'video_path' => $this->videoPath
                    ]);
                } else {
                    Log::warning('Local video file not found for deletion', [
                        'movie_id' => $this->movie->id,
                        'video_path' => $this->videoPath
                    ]);
                }
                
                // Also delete from resumable uploads directory if it exists
                $this->deleteFromResumableUploads();
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete local video file', [
                'movie_id' => $this->movie->id,
                'video_path' => $this->videoPath,
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
            // Extract filename from the video path
            $filename = basename($this->videoPath);
            $resumablePath = storage_path('app/privates/uploads/resumable/' . $filename);
            
            if (file_exists($resumablePath)) {
                unlink($resumablePath);
                
                Log::info('Resumable upload file deleted successfully', [
                    'movie_id' => $this->movie->id,
                    'filename' => $filename,
                    'resumable_path' => $resumablePath
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete resumable upload file', [
                'movie_id' => $this->movie->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception as this is cleanup operation
        }
    }

    /**
     * The job failed to process.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('HLS conversion job failed permanently', [
            'movie_id' => $this->movie->id,
            'error' => $exception->getMessage()
        ]);

        $this->movie->update([
            'hls_status' => 'failed',
            'hls_error' => $exception->getMessage()
        ]);
    }
}

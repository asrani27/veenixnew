<?php

namespace App\Http\Controllers;

use TusPhp\Config;
use App\Models\Movie;
use App\Models\Episode;
use TusPhp\Tus\Server;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\ConvertVideoToHlsJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TusUploadController extends Controller
{
    public function handle(Request $request)
    {
        $server = new Server();

        $server->setApiPath('/api/upload');

        $uploadDir = storage_path('app/public/uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            Log::info("📁 Created uploads directory: " . $uploadDir);
        }
        $server->setUploadDir($uploadDir);

        Log::info("🚀 TUS Upload initialized");

        $server->event()->addListener('tus-server.upload.complete', function ($event) {

            try {
                $file = $event->getFile();
                $filePath = $file->getFilePath();
                $details = $file->details();
                $metadata = $details['metadata'] ?? [];

                Log::info("📁 Upload completed", [
                    'file_path' => $filePath,
                    'metadata' => $metadata
                ]);

                // Get movie ID from metadata
                $movieId = $metadata['movie_id'] ?? null;
                if (!$movieId) {
                    Log::warning("⚠️ No movie_id found in upload metadata");
                    return;
                }

                // Find the movie
                $movie = Movie::find($movieId);
                if (!$movie) {
                    Log::warning("⚠️ Movie not found with ID: " . $movieId);
                    return;
                }

                // Get original file extension
                $originalFilename = $metadata['filename'] ?? '';
                $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
                if (!$extension) {
                    // Default to mp4 if no extension found
                    $extension = 'mp4';
                }

                // Create new filename with slug
                $newFilename = $movie->slug . '.' . $extension;
                $newFilePath = dirname($filePath) . '/' . $newFilename;

                // Delete existing files if they exist to ensure fresh conversion
                $this->cleanupExistingFiles($movie, $newFilename);

                // Rename the file
                if (rename($filePath, $newFilePath)) {
                    Log::info("✅ File renamed successfully", [
                        'old_path' => $filePath,
                        'new_path' => $newFilePath,
                        'new_filename' => $newFilename
                    ]);

                    // Reset movie status for fresh upload
                    $movie->update([
                        'file' => $newFilename,
                        'status_upload_to_local' => 'completed',
                        'progress_upload_to_local' => 100,
                        'status_progressive' => 'waiting',
                        'progress_progressive' => 0,
                        'progressive_file' => null,
                        'status_upload_to_wasabi' => 'waiting',
                        'progress_upload_to_wasabi' => 0,
                        'wasabi_file' => null,
                        'wasabi_url' => null
                    ]);

                    Log::info("🎬 Movie record updated with new filename and reset status", [
                        'movie_id' => $movieId,
                        'filename' => $newFilename,
                        'status_upload_to_local' => 'completed',
                        'progress_upload_to_local' => 100
                    ]);

                    // Convert to progressive MP4
                    $self = $this;
                    $self->convertToProgressiveMp4($movie, $newFilePath);
                } else {
                    Log::error("❌ Failed to rename file", [
                        'old_path' => $filePath,
                        'new_path' => $newFilePath
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("❌ Error processing upload completion", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        $response = $server->serve();
        return $response;
    }

    /**
     * Convert video to progressive MP4 format
     */
    private function convertToProgressiveMp4($movie, $inputFilePath)
    {
        try {
            Log::info("🎬 Starting progressive MP4 conversion", [
                'movie_id' => $movie->id,
                'slug' => $movie->slug,
                'input_file' => $inputFilePath
            ]);

            // Create progressive directory if it doesn't exist
            $progressiveDir = storage_path('app/public/progressive');
            if (!file_exists($progressiveDir)) {
                mkdir($progressiveDir, 0755, true);
                Log::info("📁 Created progressive directory: " . $progressiveDir);
            }

            // Update movie status to progressive conversion in progress
            $movie->update([
                'status_progressive' => 'processing',
                'progress_progressive' => 0
            ]);

            // Prepare output file path
            $outputFilename = $movie->slug . '.mp4';
            $outputFilePath = $progressiveDir . '/' . $outputFilename;

            // Build ffmpeg command
            $command = [
                'ffmpeg',
                '-i',
                $inputFilePath,
                '-movflags',
                '+faststart',
                '-c',
                'copy',
                $outputFilePath
            ];

            Log::info("🔧 Running ffmpeg command", [
                'command' => implode(' ', $command)
            ]);

            // Execute ffmpeg command
            $process = new Process($command);
            $process->setTimeout(3600); // 1 hour timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Verify output file exists
            if (!file_exists($outputFilePath)) {
                throw new \Exception("Progressive MP4 file was not created: " . $outputFilePath);
            }

            // Update movie record with progressive file info
            $movie->update([
                'progressive_file' => $outputFilename,
                'status_progressive' => 'completed',
                'progress_progressive' => 100
            ]);

            Log::info("✅ Progressive MP4 conversion completed successfully", [
                'movie_id' => $movie->id,
                'output_file' => $outputFilename,
                'file_size' => filesize($outputFilePath)
            ]);

            // Delete original file from uploads folder after successful conversion
            if (file_exists($inputFilePath)) {
                if (unlink($inputFilePath)) {
                    Log::info("🗑️ Original file deleted successfully from uploads folder", [
                        'movie_id' => $movie->id,
                        'deleted_file' => $inputFilePath
                    ]);
                } else {
                    Log::warning("⚠️ Failed to delete original file from uploads folder", [
                        'movie_id' => $movie->id,
                        'file_path' => $inputFilePath
                    ]);
                }
            } else {
                Log::warning("⚠️ Original file not found for deletion", [
                    'movie_id' => $movie->id,
                    'file_path' => $inputFilePath
                ]);
            }

            // Upload progressive file to Wasabi
            $this->uploadToWasabi($movie, $outputFilePath, $outputFilename);
        } catch (\Exception $e) {
            Log::error("❌ Progressive MP4 conversion failed", [
                'movie_id' => $movie->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update movie status to failed
            $movie->update([
                'status_progressive' => 'failed',
                'progress_progressive' => 0
            ]);
        }
    }

    /**
     * Upload progressive MP4 file to Wasabi storage
     */
    private function uploadToWasabi($movie, $localFilePath, $filename)
    {
        try {
            Log::info("☁️ Starting Wasabi upload", [
                'movie_id' => $movie->id,
                'slug' => $movie->slug,
                'local_file' => $localFilePath,
                'filename' => $filename
            ]);

            // Update movie status to Wasabi upload in progress
            $movie->update([
                'status_upload_to_wasabi' => 'processing',
                'progress_upload_to_wasabi' => 0
            ]);

            // Check if Wasabi disk is configured
            if (!config('filesystems.disks.wasabi')) {
                throw new \Exception('Wasabi disk is not configured in filesystems.php');
            }

            // Prepare Wasabi path (organize by year/month for better structure)
            $wasabiPath = $filename;

            Log::info("📤 Uploading to Wasabi", [
                'movie_id' => $movie->id,
                'wasabi_path' => $wasabiPath,
                'file_size' => filesize($localFilePath)
            ]);

            // Upload file to Wasabi using streaming for large files
            $stream = fopen($localFilePath, 'r');
            if (!$stream) {
                throw new \Exception("Failed to open file for streaming: " . $localFilePath);
            }

            // Upload with progress tracking
            $wasabiUploaded = Storage::disk('wasabi')->put($wasabiPath, $stream, [
                'visibility' => 'public',
                'metadata' => [
                    'movie_id' => $movie->id,
                    'movie_slug' => $movie->slug,
                    'original_filename' => $filename,
                    'uploaded_at' => now()->toISOString()
                ]
            ]);

            if ($stream) {
                fclose($stream);
            }

            if (!$wasabiUploaded) {
                throw new \Exception("Failed to upload file to Wasabi");
            }

            // Verify file exists on Wasabi
            if (!Storage::disk('wasabi')->exists($wasabiPath)) {
                throw new \Exception("File not found on Wasabi after upload: " . $wasabiPath);
            }

            // Get Wasabi file URL
            $wasabiUrl = Storage::disk('wasabi')->url($wasabiPath);

            // Update movie record with Wasabi file info
            $movie->update([
                'wasabi_file' => $wasabiPath,
                'wasabi_url' => $wasabiUrl,
                'status_upload_to_wasabi' => 'completed',
                'progress_upload_to_wasabi' => 100
            ]);

            Log::info("✅ Wasabi upload completed successfully", [
                'movie_id' => $movie->id,
                'wasabi_path' => $wasabiPath,
                'wasabi_url' => $wasabiUrl,
                'file_size' => filesize($localFilePath)
            ]);

            // Delete local progressive file after successful Wasabi upload to save local storage space
            if (file_exists($localFilePath)) {
                if (unlink($localFilePath)) {
                    Log::info("🗑️ Local progressive file deleted after Wasabi upload", [
                        'movie_id' => $movie->id,
                        'deleted_file' => $localFilePath
                    ]);
                } else {
                    Log::warning("⚠️ Failed to delete local progressive file", [
                        'movie_id' => $movie->id,
                        'file_path' => $localFilePath
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error("❌ Wasabi upload failed", [
                'movie_id' => $movie->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update movie status to failed
            $movie->update([
                'status_upload_to_wasabi' => 'failed',
                'progress_upload_to_wasabi' => 0
            ]);
        }
    }

    /**
     * Clean up existing files to ensure fresh conversion
     */
    private function cleanupExistingFiles($movie, $newFilename)
    {
        try {
            Log::info("🧹 Cleaning up existing files for fresh upload", [
                'movie_id' => $movie->id,
                'slug' => $movie->slug,
                'new_filename' => $newFilename
            ]);

            // Clean up uploads directory
            $uploadFilePath = storage_path('app/public/uploads/' . $newFilename);
            if (file_exists($uploadFilePath)) {
                if (unlink($uploadFilePath)) {
                    Log::info("🗑️ Deleted existing upload file", [
                        'movie_id' => $movie->id,
                        'deleted_file' => $uploadFilePath
                    ]);
                } else {
                    Log::warning("⚠️ Failed to delete existing upload file", [
                        'movie_id' => $movie->id,
                        'file_path' => $uploadFilePath
                    ]);
                }
            }

            // Clean up progressive directory
            $progressiveFilePath = storage_path('app/public/progressive/' . $movie->slug . '.mp4');
            if (file_exists($progressiveFilePath)) {
                if (unlink($progressiveFilePath)) {
                    Log::info("🗑️ Deleted existing progressive file", [
                        'movie_id' => $movie->id,
                        'deleted_file' => $progressiveFilePath
                    ]);
                } else {
                    Log::warning("⚠️ Failed to delete existing progressive file", [
                        'movie_id' => $movie->id,
                        'file_path' => $progressiveFilePath
                    ]);
                }
            }

            // Clean up Wasabi file if it exists
            if ($movie->wasabi_file) {
                try {
                    if (Storage::disk('wasabi')->exists($movie->wasabi_file)) {
                        if (Storage::disk('wasabi')->delete($movie->wasabi_file)) {
                            Log::info("🗑️ Deleted existing Wasabi file", [
                                'movie_id' => $movie->id,
                                'deleted_file' => $movie->wasabi_file
                            ]);
                        } else {
                            Log::warning("⚠️ Failed to delete existing Wasabi file", [
                                'movie_id' => $movie->id,
                                'wasabi_file' => $movie->wasabi_file
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("⚠️ Error cleaning up Wasabi file", [
                        'movie_id' => $movie->id,
                        'wasabi_file' => $movie->wasabi_file,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info("✅ Cleanup completed successfully", [
                'movie_id' => $movie->id
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error during cleanup", [
                'movie_id' => $movie->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

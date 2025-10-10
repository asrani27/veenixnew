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

class TusUploadController extends Controller
{
    public function handle(Request $request)
    {
        $server = new Server();

        $server->setApiPath('/api/upload');
        
        // Set upload directory to storage/app/public/uploads and create if not exists
        $uploadDir = storage_path('app/public/uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            Log::info("📁 Created uploads directory: " . $uploadDir);
        }
        $server->setUploadDir($uploadDir);

        Log::info("🚀 TUS Upload initialized");

        // ✅ Tambahkan event listener untuk upload complete
        $server->event()->addListener('tus-server.upload.complete', function ($event) {
            $file = $event->getFile();
            if ($file) {
                $details = $file->details();
                $metadata = $details['metadata'] ?? [];
                $movieId = $metadata['movie_id'] ?? null;
                $episodeId = $metadata['episode_id'] ?? null;
                $filename = basename($file->getFilePath());
                $filePath = $file->getFilePath();

                // Check video file integrity
                $isCorrupted = $this->checkVideoIntegrity($filePath);
                
                if ($isCorrupted) {
                    Log::error("🚨 Video file is corrupted: " . $filename);
                    // Delete corrupted file
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    return;
                }

                Log::info("✅ Video file integrity check passed: " . $filename);

                // Handle File Movie Upload
                if ($movieId) {
                    $movie = Movie::find($movieId);
                    if ($movie) {
                        $movie->update([
                            'file' => $filename,
                            'hls_status' => null,
                            'hls_progress' => 0
                        ]);
                        Log::info("📹 Movie file updated and HLS status reset for movie ID: " . $movieId);
                    } else {
                        Log::warning("⚠️ Movie not found with ID: " . $movieId);
                    }
                }
                // Handle File Episode Upload
                elseif ($episodeId) {
                    $episode = Episode::find($episodeId);
                    if ($episode) {
                        $episode->update([
                            'file' => $filename,
                            'hls_status' => null,
                            'hls_progress' => 0
                        ]);
                        Log::info("📹 Episode file updated and HLS status reset for episode ID: " . $episodeId);
                    } else {
                        Log::warning("⚠️ Episode not found with ID: " . $episodeId);
                    }
                } else {
                    Log::warning("⚠️ No movie_id or episode_id found in metadata");
                }
            } else {
            }
        });

        $response = $server->serve();
        return $response;
    }

    /**
     * Check video file integrity using FFprobe
     */
    private function checkVideoIntegrity($filePath)
    {
        try {
            // Check if file exists
            if (!file_exists($filePath)) {
                Log::error("File does not exist: " . $filePath);
                return true; // Treat as corrupted
            }

            // Check file size
            $fileSize = filesize($filePath);
            if ($fileSize === 0) {
                Log::error("File is empty: " . $filePath);
                return true; // Empty file is corrupted
            }

            // Use FFprobe to check video integrity
            $ffprobePath = $this->findFFprobe();
            if (!$ffprobePath) {
                Log::warning("FFprobe not found, skipping integrity check for: " . $filePath);
                return false; // Assume not corrupted if we can't check
            }

            $command = sprintf(
                '%s -v error -show_format -show_streams %s 2>&1',
                escapeshellarg($ffprobePath),
                escapeshellarg($filePath)
            );

            $output = shell_exec($command);
            $returnCode = 0;

            // Check if FFprobe executed successfully
            if ($output === null) {
                Log::error("FFprobe failed to execute for: " . $filePath);
                return true; // Assume corrupted
            }

            // Check for errors in output
            if (strpos($output, 'Invalid data found when processing input') !== false ||
                strpos($output, 'corrupt') !== false ||
                strpos($output, 'truncated') !== false ||
                strpos($output, 'Invalid') !== false) {
                Log::error("FFprobe detected corruption in: " . $filePath . " Output: " . $output);
                return true; // File is corrupted
            }

            // Try to extract basic video information
            $hasVideoStream = strpos($output, 'codec_type=video') !== false;
            $hasAudioStream = strpos($output, 'codec_type=audio') !== false;

            if (!$hasVideoStream && !$hasAudioStream) {
                Log::error("No valid audio/video streams found in: " . $filePath);
                return true; // File is corrupted
            }

            // Additional check: try to get duration
            preg_match('/duration=([0-9.]+)/', $output, $matches);
            if (isset($matches[1])) {
                $duration = floatval($matches[1]);
                if ($duration <= 0) {
                    Log::error("Invalid duration found in: " . $filePath . " Duration: " . $duration);
                    return true; // File is corrupted
                }
            }

            Log::info("✅ Video integrity check passed for: " . $filePath . " Size: " . $fileSize . " bytes");
            return false; // File is not corrupted

        } catch (\Exception $e) {
            Log::error("Error checking video integrity for " . $filePath . ": " . $e->getMessage());
            return true; // Assume corrupted on error
        }
    }

    /**
     * Find FFprobe executable path
     */
    private function findFFprobe()
    {
        // Common paths for FFprobe
        $paths = [
            '/usr/bin/ffprobe',
            '/usr/local/bin/ffprobe',
            '/opt/homebrew/bin/ffprobe',
            'ffprobe' // Try system PATH
        ];

        foreach ($paths as $path) {
            if (is_executable($path) || $path === 'ffprobe') {
                // Test if ffprobe actually works
                $testCommand = $path === 'ffprobe' ? 'ffprobe -version' : escapeshellarg($path) . ' -version';
                $output = shell_exec($testCommand . ' 2>&1');
                
                if ($output && strpos($output, 'ffprobe') !== false) {
                    return $path;
                }
            }
        }

        return null;
    }
}

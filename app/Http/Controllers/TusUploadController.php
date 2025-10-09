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
        $server->setUploadDir(storage_path('app/uploads'));

        Log::info("🚀 TUS Upload initialized");
        
        // ✅ Tambahkan event listener untuk upload complete
        $server->event()->addListener('tus-server.upload.complete', function ($event) {
            $file = $event->getFile();
            if ($file) {
                $details = $file->details();
                $metadata = $details['metadata'] ?? [];
                $movieId = $metadata['movie_id'] ?? null;
                $episodeId = $metadata['episode_id'] ?? null;
                $filepath = $file->getFilePath();
                
                // Buat path relatif untuk job
                $inputPath = str_replace(storage_path('app') . '/', '', $filepath);
                $outputDir = 'hls_videos';

                // Handle Movie Upload
                if ($movieId) {
                    $movie = Movie::find($movieId);
                    if ($movie) {
                        $filename = $movie->slug;
                        Log::info("✅ Movie Upload complete: {$filename} ({$filepath}), movie_id={$movieId}");

                        // Update movie file field
                        $movie->update(['file' => $filename]);

                        // Dispatch unified convert job for movie
                        Log::info("🚀 Dispatch ConvertVideoToHlsJob for movie: {$filename}");
                        ConvertVideoToHlsJob::dispatch($inputPath, $outputDir, $filename, $movieId, null);
                    } else {
                        Log::warning("⚠️ Movie not found for movie_id={$movieId}");
                    }
                }
                // Handle Episode Upload
                elseif ($episodeId) {
                    $episode = Episode::find($episodeId);
                    if ($episode) {
                        $tvShow = $episode->tv;
                        $filename = Str::slug($tvShow->title) . '-s' . str_pad($episode->season_number, 2, '0', STR_PAD_LEFT) . 'e' . str_pad($episode->episode_number, 2, '0', STR_PAD_LEFT);
                        Log::info("✅ Episode Upload complete: {$filename} ({$filepath}), episode_id={$episodeId}");

                        // Update episode file field
                        $episode->update(['file' => $filename]);

                        // Dispatch unified convert job for episode
                        Log::info("🚀 Dispatch ConvertVideoToHlsJob for episode: {$filename}");
                        ConvertVideoToHlsJob::dispatch($inputPath, $outputDir, $filename, null, $episodeId);
                    } else {
                        Log::warning("⚠️ Episode not found for episode_id={$episodeId}");
                    }
                }
                // No valid content type found
                else {
                    Log::warning("⚠️ Upload complete but no movie_id or episode_id found in metadata");
                    Log::info("Available metadata: " . json_encode($metadata));
                }
            } else {
                Log::warning('⚠️ Upload complete event triggered but file is null.');
            }
        });
        
        $response = $server->serve();
        return $response;
    }
}

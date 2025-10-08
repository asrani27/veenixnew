<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use TusPhp\Tus\Server;
use Illuminate\Http\Request;
use App\Jobs\ConvertVideoToHlsJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TusUploadController extends Controller
{
    public function handle(Request $request)
    {
        $server = new Server();

        $server->setApiPath('/api/upload');
        $server->setUploadDir(storage_path('app/uploads'));

        $cacheDir = storage_path('app/tus-cache');
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $server->setCacheDir($cacheDir);

        // ✅ Tambahkan event listener untuk upload complete
        $server->event()->addListener('tus-server.upload.complete', function ($event) {
            $file = $event->getFile();
            if ($file) {
                $details = $file->details();
                $metadata = $details['metadata'] ?? [];
                $movieId = $metadata['movie_id'] ?? null;
                $filename = Movie::find($movieId)->slug;
                $filepath = $file->getFilePath();
                Log::info("✅ Upload complete: {$filename} ({$filepath}), movie_id={$movieId}");

                // Buat path relatif untuk job
                $inputPath = str_replace(storage_path('app') . '/', '', $filepath);
                $outputDir = 'hls_videos';

                // Dispatch job convert
                Log::info("🚀 Dispatch ConvertVideoToHlsJob for {$filename}");
                ConvertVideoToHlsJob::dispatch($inputPath, $outputDir, $filename, $movieId);
            } else {
                Log::warning('⚠️ Upload complete event triggered but file is null.');
            }
        });
        $response = $server->serve();
        return $response;
    }
}

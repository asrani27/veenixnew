<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Post;
use App\Models\Upload;
use App\Models\Tv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StreamController extends Controller
{
    public function movieStream($tmdb_id)
    {

        $data = Movie::where('tmdb_id', $tmdb_id)->first();

        if (!$data || empty($data->hls_master_playlist_path)) {
            return view('stream', [
                'urlVideo' => null,
                'errorMessage' => "Video Tidak Bisa Di Putar"
            ]);
        }

        $filePath = $data->hls_master_playlist_path;
        $errorMessage = null;
        $isHls = false;
        // Check if file is HLS (m3u8)
        if (str_ends_with(strtolower($filePath), '.m3u8')) {
            $isHls = true;
            // For HLS, we need to modify the m3u8 content to use proxy URLs
            $urlVideo = $this->getHlsProxyUrl($filePath);
        } else {
            // Regular video file
            if (!session()->has("video_url_{$tmdb_id}")) {
                $urlVideo = Storage::disk('wasabi')->temporaryUrl(
                    $filePath,
                    now()->addMinutes(180)
                );
                session(["video_url_{$tmdb_id}" => $urlVideo]);
            } else {
                $urlVideo = session("video_url_{$tmdb_id}");
            }
        }

        return view('stream', compact('urlVideo', 'errorMessage', 'isHls'));
    }

    public function tvStream($tmdb_id)
    {
        $data = Tv::where('tmdb_id', $tmdb_id)->first();

        if (!$data || empty($data->file)) {
            return view('stream', [
                'urlVideo' => null,
                'errorMessage' => "Video Tidak Bisa Di Putar"
            ]);
        }

        $filePath = $data->file;
        $errorMessage = null;
        $isHls = false;

        // Check if file is HLS (m3u8)
        if (str_ends_with(strtolower($filePath), '.m3u8')) {
            $isHls = true;
            // For HLS, we need to modify the m3u8 content to use proxy URLs
            $urlVideo = $this->getHlsProxyUrl($filePath);
        } else {
            // Regular video file
            if (!session()->has("tv_video_url_{$tmdb_id}")) {
                $urlVideo = Storage::disk('wasabi')->temporaryUrl(
                    $filePath,
                    now()->addMinutes(180)
                );
                session(["tv_video_url_{$tmdb_id}" => $urlVideo]);
            } else {
                $urlVideo = session("tv_video_url_{$tmdb_id}");
            }
        }

        return view('stream', compact('urlVideo', 'errorMessage', 'isHls'));
    }

    public function tvStreamBySlug($slug)
    {
        $data = Tv::where('slug', $slug)->first();

        if (!$data || empty($data->file)) {
            return view('stream', [
                'urlVideo' => null,
                'errorMessage' => "Video Tidak Bisa Di Putar"
            ]);
        }

        $filePath = $data->file;
        $errorMessage = null;
        $isHls = false;

        // Check if file is HLS (m3u8)
        if (str_ends_with(strtolower($filePath), '.m3u8')) {
            $isHls = true;
            // For HLS, we need to modify the m3u8 content to use proxy URLs
            $urlVideo = $this->getHlsProxyUrl($filePath);
        } else {
            // Regular video file
            if (!session()->has("tv_video_url_slug_{$slug}")) {
                $urlVideo = Storage::disk('wasabi')->temporaryUrl(
                    $filePath,
                    now()->addMinutes(180)
                );
                session(["tv_video_url_slug_{$slug}" => $urlVideo]);
            } else {
                $urlVideo = session("tv_video_url_slug_{$slug}");
            }
        }

        return view('stream', compact('urlVideo', 'errorMessage', 'isHls'));
    }

    public function episodeStream($slug, $season_number, $episode_number)
    {
        try {
            // Get TV show by slug
            $tv = Tv::with(['seasons.episodes'])->where('slug', $slug)->firstOrFail();

            // Find the specific episode
            $episode = null;
            $season = null;

            foreach ($tv->seasons as $tvSeason) {
                if ($tvSeason->season_number == $season_number) {
                    $season = $tvSeason;
                    foreach ($tvSeason->episodes as $tvEpisode) {
                        if ($tvEpisode->episode_number == $episode_number) {
                            $episode = $tvEpisode;
                            break;
                        }
                    }
                    break;
                }
            }

            $urlVideo = null;
            $errorMessage = null;
            $isHls = false;

            if (!$episode || !$season) {
                // Episode not found, set error message
                $errorMessage = "Video Tidak Bisa Di Putar ";
            } else {
                // Episode found, get the video URL
                $filePath = $episode->hls_playlist_path;

                // Check if HLS playlist path exists and is not null
                if (!empty($filePath)) {
                    // HLS playlist is always m3u8 format
                    $isHls = true;
                    // For HLS, we need to modify the m3u8 content to use proxy URLs
                    $urlVideo = $this->getHlsProxyUrl($filePath);
                } else {
                    // Fallback to original file if HLS playlist is not available
                    $originalFile = $episode->file;
                    if (!empty($originalFile)) {
                        // Check if file is HLS (m3u8)
                        if (str_ends_with(strtolower($originalFile), '.m3u8')) {
                            $isHls = true;
                            // For HLS, we need to modify the m3u8 content to use proxy URLs
                            $urlVideo = $this->getHlsProxyUrl($originalFile);
                        } else {
                            // Regular video file - use episode-specific session key
                            $sessionKey = "episode_video_url_{$episode->id}";
                            if (!session()->has($sessionKey)) {
                                $urlVideo = Storage::disk('wasabi')->temporaryUrl(
                                    $originalFile,
                                    now()->addMinutes(180)
                                );

                                // Save to session
                                session([$sessionKey => $urlVideo]);
                            } else {
                                $urlVideo = session($sessionKey);
                            }
                        }
                    } else {
                        // Both HLS playlist and original file are null or empty
                        $errorMessage = "Video Tidak Bisa Di Putar";
                    }
                }
            }

            return view('stream', compact('urlVideo', 'tv', 'season', 'episode', 'errorMessage', 'isHls'));
        } catch (\Exception $e) {
            // TV show not found, still show the page with error message
            return view('stream', [
                'urlVideo' => null,
                'tv' => null,
                'season' => null,
                'episode' => null,
                'errorMessage' => "Video Tidak Bisa Di Putar"
            ]);
        }
    }

    /**
     * HLS Proxy for serving HLS segments from Wasabi
     */
    public function hlsProxy($path)
    {
        try {
            // Validate the path to prevent directory traversal
            $path = str_replace('..', '', $path);
            $path = ltrim($path, '/');

            // Construct the full Wasabi path
            $wasabiPath = $path;

            // Check if file exists in Wasabi
            if (!Storage::disk('wasabi')->exists($wasabiPath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Generate temporary URL for the file
            $temporaryUrl = Storage::disk('wasabi')->temporaryUrl(
                $wasabiPath,
                now()->addMinutes(5) // Short expiry for security
            );

            // Get the file content from Wasabi
            $response = Http::timeout(30)->get($temporaryUrl);

            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch file'], 500);
            }

            // Determine content type based on file extension
            $extension = strtolower(pathinfo($wasabiPath, PATHINFO_EXTENSION));
            $contentType = $this->getContentType($extension);

            // Return the file content with appropriate headers
            return response($response->body())
                ->header('Content-Type', $contentType)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Range')
                ->header('Accept-Ranges', 'bytes')
                ->header('Cache-Control', 'public, max-age=300'); // 5 minutes cache

        } catch (\Exception $e) {
            \Log::error('HLS Proxy Error: ' . $e->getMessage(), [
                'path' => $path,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Proxy error'], 500);
        }
    }

    /**
     * Get HLS proxy URL for m3u8 file
     */
    private function getHlsProxyUrl($filePath)
    {
        // Generate temporary URL for the m3u8 file
        $temporaryUrl = Storage::disk('wasabi')->temporaryUrl(
            $filePath,
            now()->addMinutes(180)
        );

        // Get the m3u8 content
        $response = Http::timeout(30)->get($temporaryUrl);

        if (!$response->successful()) {
            return null;
        }

        $m3u8Content = $response->body();
        $baseDir = dirname($filePath);

        // Replace segment URLs with proxy URLs
        $modifiedContent = preg_replace_callback(
            '/^(?!#)(.+)$/m',
            function ($matches) use ($baseDir) {
                $segment = trim($matches[1]);
                if (empty($segment) || str_starts_with($segment, '#')) {
                    return $segment;
                }

                // Construct full path for the segment
                $fullPath = $baseDir . '/' . $segment;
                $proxyUrl = url('/hls-proxy/' . ltrim($fullPath, '/'));

                return $proxyUrl;
            },
            $m3u8Content
        );

        // Create a data URL for the modified m3u8 content
        $base64Content = base64_encode($modifiedContent);
        return 'data:application/vnd.apple.mpegurl;base64,' . $base64Content;
    }

    /**
     * Get content type based on file extension
     */
    private function getContentType($extension)
    {
        $contentTypes = [
            'm3u8' => 'application/vnd.apple.mpegurl',
            'ts' => 'video/mp2t',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'vtt' => 'text/vtt',
            'srt' => 'application/x-subrip',
        ];

        return $contentTypes[$extension] ?? 'application/octet-stream';
    }
}

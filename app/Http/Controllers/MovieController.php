<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\TmdbService;
use App\Jobs\ConvertVideoToHlsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    protected $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function index()
    {
        $data = Movie::with(['genres', 'countries'])->orderBy('id', 'DESC')->paginate(10);
        return view('admin.movie.index', compact('data'));
    }

    public function create()
    {
        return view('admin.movie.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'year' => 'required|integer',
            'description' => 'required|string',
        ]);

        Movie::create($request->all());

        return redirect()->route('admin.movies.index')->with('success', 'Movie created successfully.');
    }

    /**
     * Show TMDB movie search page
     */
    public function searchTmdb()
    {
        return view('admin.movie.search-tmdb');
    }

    /**
     * Search movies from TMDB API
     */
    public function searchTmdbApi(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'page' => 'sometimes|integer|min:1'
        ]);

        try {
            $page = $request->input('page', 1);
            $query = $request->input('query');
            $response = $this->tmdbService->searchMovies($query, $page);

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show popular movies from TMDB
     */
    public function popularTmdb(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $type = $request->input('type', 'popular'); // popular, top_rated, upcoming, now_playing

            switch ($type) {
                case 'top_rated':
                    $response = $this->tmdbService->getTopRatedMovies($page);
                    break;
                case 'upcoming':
                    $response = $this->tmdbService->getUpcomingMovies($page);
                    break;
                case 'now_playing':
                    $response = $this->tmdbService->getNowPlayingMovies($page);
                    break;
                default:
                    $response = $this->tmdbService->getPopularMovies($page);
            }

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import movie from TMDB
     */
    public function importFromTmdb($tmdbId)
    {
        try {
            // Check if movie already exists
            $existingMovie = Movie::where('tmdb_id', $tmdbId)->first();
            if ($existingMovie) {
                return response()->json([
                    'success' => false,
                    'message' => 'Movie already exists'
                ], 400);
            }

            // Get movie details from TMDB
            $movieData = $this->tmdbService->getMovieDetails($tmdbId);
            $formattedData = $this->tmdbService->formatMovieData($movieData);

            // Download and save poster
            $localPosterPath = $this->tmdbService->downloadAndSavePoster($movieData['poster_path'], $tmdbId);
            $localBackdropPath = $this->tmdbService->downloadAndSaveBackdrop($movieData['backdrop_path'], $tmdbId);

            // Add local paths to data
            $formattedData['local_poster_path'] = $localPosterPath;
            $formattedData['local_backdrop_path'] = $localBackdropPath;

            // Create movie record
            $movie = Movie::create($formattedData);

            // Import genres
            $genreIds = $this->tmdbService->importGenres($movieData);
            if (!empty($genreIds)) {
                $movie->genres()->attach($genreIds);
            }

            // Import production countries
            $countryIds = $this->tmdbService->importCountries($movieData);
            if (!empty($countryIds)) {
                $movie->countries()->attach($countryIds);
            }

            // Import actors
            $actorsData = $this->tmdbService->importActors($movieData);
            if (!empty($actorsData)) {
                $movie->actors()->attach($actorsData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Movie imported successfully with genres, countries, and actors',
                'data' => $movie
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movie details from TMDB
     */
    public function getTmdbDetails($tmdbId)
    {
        try {
            $movieData = $this->tmdbService->getMovieDetails($tmdbId);

            // Add image URLs
            $movieData['poster_url'] = $this->tmdbService->getImageUrl($movieData['poster_path']);
            $movieData['backdrop_url'] = $this->tmdbService->getBackdropUrl($movieData['backdrop_path']);

            return response()->json([
                'success' => true,
                'data' => $movieData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk import movies from TMDB
     */
    public function bulkImportTmdb(Request $request)
    {
        $request->validate([
            'tmdb_ids' => 'required|array|min:1',
            'tmdb_ids.*' => 'required|integer|min:1'
        ]);

        try {
            $imported = [];
            $skipped = [];

            foreach ($request->tmdb_ids as $tmdbId) {
                // Check if movie already exists
                $existingMovie = Movie::where('tmdb_id', $tmdbId)->first();
                if ($existingMovie) {
                    $skipped[] = $tmdbId;
                    continue;
                }

                // Get movie details from TMDB
                $movieData = $this->tmdbService->getMovieDetails($tmdbId);
                $formattedData = $this->tmdbService->formatMovieData($movieData);

                // Download and save poster and backdrop
                $localPosterPath = $this->tmdbService->downloadAndSavePoster($movieData['poster_path'], $tmdbId);
                $localBackdropPath = $this->tmdbService->downloadAndSaveBackdrop($movieData['backdrop_path'], $tmdbId);

                // Add local paths to data
                $formattedData['local_poster_path'] = $localPosterPath;
                $formattedData['local_backdrop_path'] = $localBackdropPath;

                // Create movie record
                $movie = Movie::create($formattedData);

                // Import genres
                $genreIds = $this->tmdbService->importGenres($movieData);
                if (!empty($genreIds)) {
                    $movie->genres()->attach($genreIds);
                }

                // Import production countries
                $countryIds = $this->tmdbService->importCountries($movieData);
                if (!empty($countryIds)) {
                    $movie->countries()->attach($countryIds);
                }

                // Import actors
                $actorsData = $this->tmdbService->importActors($movieData);
                if (!empty($actorsData)) {
                    $movie->actors()->attach($actorsData);
                }

                $imported[] = $movie;
            }

            return response()->json([
                'success' => true,
                'message' => "Imported " . count($imported) . " movies with genres, countries, and actors, skipped " . count($skipped) . " movies",
                'data' => [
                    'imported' => $imported,
                    'skipped' => $skipped
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified movie.
     */
    public function edit($id)
    {
        $movie = Movie::findOrFail($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified movie in storage.
     */
    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'original_title' => 'nullable|string|max:255',
            'overview' => 'nullable|string',
            'description' => 'nullable|string',
            'poster_path' => 'nullable|string|max:255',
            'backdrop_path' => 'nullable|string|max:255',
            'local_poster_path' => 'nullable|string|max:255',
            'local_backdrop_path' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'runtime' => 'nullable|integer',
            'vote_average' => 'nullable|numeric|min:0|max:10',
            'vote_count' => 'nullable|integer|min:0',
            'popularity' => 'nullable|numeric|min:0',
            'adult' => 'nullable|boolean',
            'original_language' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:50',
            'tagline' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
            'file' => 'nullable|string|max:255',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            'countries' => 'nullable|array',
            'countries.*' => 'exists:countries,id',
            'actors' => 'nullable|array',
            'actors.*' => 'exists:actors,id',
            'download_links' => 'nullable|array',
            'download_links.*.url' => 'nullable|string',
            'download_links.*.quality' => 'nullable|in:540p,720p',
            'download_links.*.label' => 'nullable|string',
            'download_links.*.is_active' => 'nullable|boolean',
            'download_links.*.sort_order' => 'nullable|integer|min:0',
        ]);

        // Update movie basic data
        $movie->update($request->except(['genres', 'countries', 'actors', 'download_links']));

        // Update genres relationship
        if ($request->has('genres')) {
            $movie->genres()->sync($request->genres);
        }

        // Update countries relationship
        if ($request->has('countries')) {
            $movie->countries()->sync($request->countries);
        }

        // Update actors relationship
        if ($request->has('actors')) {
            $movie->actors()->sync($request->actors);
        }

        // Update download links
        $this->updateDownloadLinks($movie, $request->download_links ?? []);

        return redirect()->route('admin.movies.index')->with('success', 'Movie updated successfully.');
    }

    /**
     * Update download links for a movie
     */
    private function updateDownloadLinks($movie, $downloadLinksData)
    {
        // Get existing download links
        $existingLinks = $movie->downloadLinks->keyBy('id');
        $updatedLinkIds = [];

        if (empty($downloadLinksData)) {
            // If no download links data provided, delete all existing links
            $movie->downloadLinks()->delete();
            return;
        }

        foreach ($downloadLinksData as $key => $linkData) {
            // Check if this is a new link (key starts with "new_") or existing link
            if (str_starts_with($key, 'new_')) {
                // This is a new link, create it
                $newLink = $movie->downloadLinks()->create([
                    'url' => $linkData['url'] ?? '',
                    'quality' => $linkData['quality'] ?? '540p',
                    'label' => $linkData['label'] ?? null,
                    'is_active' => isset($linkData['is_active']) ? (bool)$linkData['is_active'] : true,
                    'sort_order' => $linkData['sort_order'] ?? 0,
                ]);
                $updatedLinkIds[] = $newLink->id; // Add new link to updated list to prevent deletion
            } else {
                // This is an existing link
                if (isset($linkData['id']) && isset($existingLinks[$linkData['id']])) {
                    $link = $existingLinks[$linkData['id']];
                    $link->update([
                        'url' => $linkData['url'] ?? $link->url,
                        'quality' => $linkData['quality'] ?? $link->quality,
                        'label' => $linkData['label'] ?? $link->label,
                        'is_active' => isset($linkData['is_active']) ? (bool)$linkData['is_active'] : $link->is_active,
                        'sort_order' => $linkData['sort_order'] ?? $link->sort_order,
                    ]);
                    $updatedLinkIds[] = $link->id;
                }
            }
        }

        // Delete links that weren't updated (removed from form)
        $movie->downloadLinks()
            ->whereNotIn('id', $updatedLinkIds)
            ->delete();
    }

    /**
     * Remove the specified movie from storage.
     */
    public function destroy($id)
    {
        try {
            $movie = Movie::findOrFail($id);

            // Delete local poster if exists
            if ($movie->local_poster_path) {
                Storage::disk('public')->delete($movie->local_poster_path);
            }

            // Delete local backdrop if exists
            if ($movie->local_backdrop_path) {
                Storage::disk('public')->delete($movie->local_backdrop_path);
            }

            // Delete video files from Wasabi storage
            $this->deleteMovieVideoFiles($movie);

            // Delete movie record
            $movie->delete();

            return redirect()->route('admin.movies.index')->with('success', 'Movie and all associated files deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete movie: ' . $e->getMessage(), [
                'movie_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('admin.movies.index')->with('error', 'Failed to delete movie: ' . $e->getMessage());
        }
    }

    /**
     * Delete all video files associated with a movie from Wasabi storage
     */
    private function deleteMovieVideoFiles($movie)
    {
        try {
            // Delete original video file if exists
            if ($movie->file) {
                $this->deleteVideoFromWasabi($movie->file);
            }

            // Delete HLS files if they exist
            if ($movie->hls_master_playlist_path) {
                $this->deleteHlsFilesFromWasabi($movie->slug);
            }

            Log::info('Video files deleted successfully for movie', [
                'movie_id' => $movie->id,
                'movie_slug' => $movie->slug
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete video files from Wasabi', [
                'movie_id' => $movie->id,
                'movie_slug' => $movie->slug,
                'error' => $e->getMessage()
            ]);
            // Don't throw the error to prevent movie deletion failure
            // Just log it for monitoring
        }
    }

    /**
     * Delete video file from Wasabi storage
     */
    private function deleteVideoFromWasabi($filePath)
    {
        try {
            if (!$filePath) {
                return;
            }

            // Check if file exists in Wasabi
            if (Storage::disk('wasabi')->exists($filePath)) {
                Storage::disk('wasabi')->delete($filePath);
                Log::info('Video file deleted from Wasabi', ['file_path' => $filePath]);
            } else {
                Log::warning('Video file not found in Wasabi', ['file_path' => $filePath]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete video file from Wasabi', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete all HLS files from Wasabi storage for a movie
     */
    private function deleteHlsFilesFromWasabi($movieSlug)
    {
        try {
            if (!$movieSlug) {
                return;
            }

            // List all files in the movie's HLS directory
            $files = Storage::disk('wasabi')->files($movieSlug);

            // Delete each file
            $deletedCount = 0;
            foreach ($files as $file) {
                try {
                    Storage::disk('wasabi')->delete($file);
                    $deletedCount++;
                } catch (\Exception $e) {
                    Log::warning('Failed to delete HLS file', [
                        'file' => $file,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Try to delete the directory itself
            try {
                Storage::disk('wasabi')->deleteDirectory($movieSlug);
            } catch (\Exception $e) {
                // Some storage systems don't support directory deletion
                Log::info('Could not delete directory (files already deleted)', [
                    'directory' => $movieSlug,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('HLS files deleted from Wasabi', [
                'movie_slug' => $movieSlug,
                'files_deleted' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete HLS files from Wasabi', [
                'movie_slug' => $movieSlug,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Upload video for movie
     */
    public function uploadVideo(Request $request, $movieId)
    {
        try {
            $request->validate([
                'video_file' => 'required|file|mimetypes:video/mp4|max:512000', // 500MB max
            ]);

            $movie = Movie::findOrFail($movieId);
            $videoFile = $request->file('video_file');

            // Delete existing video if it exists
            if ($movie->file) {
                $this->deleteExistingVideo($movie->file);
            }

            // Create temporary directory for processing
            $tempDir = storage_path('app/temp/' . uniqid('video_', true));
            mkdir($tempDir, 0755, true);

            // Move uploaded file to temp directory
            $inputPath = $tempDir . '/input.mp4';
            move_uploaded_file($videoFile->getPathname(), $inputPath);

            // Convert MP4 to HLS
            $hlsPath = $this->convertToHls($inputPath, $tempDir);

            // Upload HLS files to Wasabi
            $wasabiPath = $this->uploadHlsToWasabi($hlsPath, $movieId);

            // Update movie with file path
            $movie->update(['file' => $wasabiPath]);

            // Clean up temporary files
            $this->cleanupTempFiles($tempDir);

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded and converted successfully',
                'path' => $wasabiPath
            ]);
        } catch (\Exception $e) {
            // Clean up on error
            if (isset($tempDir) && is_dir($tempDir)) {
                $this->cleanupTempFiles($tempDir);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error uploading video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get HLS conversion status for a movie
     */
    public function getHlsStatus($id)
    {
        $movie = Movie::findOrFail($id);

        return response()->json([
            'status' => $movie->hls_status,
            'progress' => $movie->hls_progress ?? 0,
            'message' => $this->getHlsStatusMessage($movie),
            'error_message' => $movie->hls_error_message,
            'master_playlist_url' => $movie->hls_master_playlist_url,
            'master_playlist_path' => $movie->hls_master_playlist_url, // For frontend compatibility
            'processed_at' => $movie->hls_processed_at?->toISOString(),
        ]);
    }

    /**
     * Get human-readable HLS status message
     */
    private function getHlsStatusMessage($movie)
    {
        $status = $movie->hls_status;
        $progress = $movie->hls_progress ?? 0;

        // Provide more detailed messages based on progress
        if ($status === 'processing') {
            if ($progress < 20) {
                return 'Initializing conversion...';
            } elseif ($progress < 40) {
                return 'Encoding video stream...';
            } elseif ($progress < 60) {
                return 'Encoding audio stream...';
            } elseif ($progress < 75) {
                return 'Creating HLS segments...';
            } else {
                return 'Finalizing conversion...';
            }
        }

        return match ($status) {
            'pending' => 'Waiting to start conversion...',
            'processing' => 'Converting to HLS format...',
            'completed' => 'Conversion completed successfully!',
            'failed' => $movie->hls_error_message ?: 'Conversion failed. Please try again.',
            default => 'Unknown status',
        };
    }

    /**
     * Upload HLS files to Wasabi
     */
    private function uploadHlsToWasabi($hlsPath, $movieId)
    {
        // Get movie to access slug
        $movie = Movie::findOrFail($movieId);

        // Create folder structure: veenix/{slug}/
        $wasabiPath = $movie->slug;

        // Get all files in HLS directory
        $files = glob($hlsPath . '/*');

        foreach ($files as $file) {
            $filename = basename($file);
            $remotePath = $wasabiPath . '/' . $filename;

            // Upload to Wasabi
            Storage::disk('wasabi')->put($remotePath, file_get_contents($file));
        }

        return $wasabiPath . '/playlist.m3u8';
    }

    /**
     * Clean up temporary files
     */
    private function cleanupTempFiles($tempDir)
    {
        if (is_dir($tempDir)) {
            $this->removeDirectory($tempDir);
        }
    }

    /**
     * Recursively remove a directory and its contents
     */
    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * Delete existing video files from Wasabi storage
     */
    private function deleteExistingVideo($filePath)
    {
        try {
            if (!$filePath) {
                return;
            }

            // Extract the directory path from the file path
            // Example: "movie-slug/playlist.m3u8" -> "movie-slug/"
            $directoryPath = dirname($filePath);

            // If the file path doesn't contain a directory, use the file path as directory
            if ($directoryPath === '.' || $directoryPath === '') {
                $directoryPath = $filePath;
            }

            // List all files in the directory
            $files = Storage::disk('wasabi')->files($directoryPath);

            // Delete all files in the directory
            foreach ($files as $file) {
                Storage::disk('wasabi')->delete($file);
            }

            // Try to delete the directory itself (if the storage supports it)
            try {
                Storage::disk('wasabi')->deleteDirectory($directoryPath);
            } catch (\Exception $e) {
                // Some storage systems don't support directory deletion
                // or the directory might not be empty, which is fine
            }
        } catch (\Exception $e) {
            // Log the error but don't throw it to prevent upload failure
            // You might want to log this error in a real application
            Log::error('Failed to delete existing video: ' . $e->getMessage());
        }
    }

    /**
     * Retry HLS conversion for a movie
     */
    public function retryHlsConversion($movieId)
    {
        try {
            $movie = Movie::findOrFail($movieId);

            // Check if movie has a video file
            if (!$movie->file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No video file found for this movie'
                ], 400);
            }

            // Reset HLS status
            $movie->update([
                'hls_status' => 'pending',
                'hls_progress' => 0,
                'hls_error_message' => null
            ]);

            // Dispatch the conversion job
            ConvertVideoToHlsJob::dispatch($movie->id, $movie->file, $movie->slug);

            Log::info('HLS conversion retry dispatched', [
                'movie_id' => $movieId,
                'movie_slug' => $movie->slug
            ]);

            return response()->json([
                'success' => true,
                'message' => 'HLS conversion retry started successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrying HLS conversion', [
                'movie_id' => $movieId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retry HLS conversion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the movie detail page.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        $movie = Movie::where('slug', $slug)
            ->with(['genres', 'countries', 'actors'])
            ->firstOrFail();

        // Get related movies (same genres, exclude current movie)
        $relatedMovies = Movie::whereHas('genres', function ($query) use ($movie) {
            $query->whereIn('genre_id', $movie->genres->pluck('id'));
        })
            ->where('id', '!=', $movie->id)
            ->whereNotNull('poster_path')
            ->inRandomOrder()
            ->take(12)
            ->get();

        return view('movie', compact('movie', 'relatedMovies'));
    }

    /**
     * Show download page for the specified movie.
     */
    public function download($slug)
    {
        $movie = Movie::where('slug', $slug)
            ->with(['genres', 'countries', 'actors', 'downloadLinks' => function ($query) {
                $query->where('is_active', true)->ordered();
            }])
            ->firstOrFail();


        return view('movie-download', compact('movie'));
    }
}

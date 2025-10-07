<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class TmdbService
{
    protected $apiKey;
    protected $baseUrl;
    protected $imageUrl;

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
        $this->baseUrl = 'https://api.themoviedb.org/3';
        $this->imageUrl = 'https://image.tmdb.org/t/p';
        
        // Debug logging
        Log::info('TmdbService initialized', [
            'api_key_present' => !empty($this->apiKey),
            'api_key_length' => strlen($this->apiKey ?? ''),
            'api_key_prefix' => substr($this->apiKey ?? '', 0, 8) . '...'
        ]);
    }

    /**
     * Get popular movies
     */
    public function getPopularMovies($page = 1)
    {
        return $this->makeRequest("/movie/popular", ['page' => $page]);
    }

    /**
     * Get top rated movies
     */
    public function getTopRatedMovies($page = 1)
    {
        return $this->makeRequest("/movie/top_rated", ['page' => $page]);
    }

    /**
     * Get upcoming movies
     */
    public function getUpcomingMovies($page = 1)
    {
        return $this->makeRequest("/movie/upcoming", ['page' => $page]);
    }

    /**
     * Get now playing movies
     */
    public function getNowPlayingMovies($page = 1)
    {
        return $this->makeRequest("/movie/now_playing", ['page' => $page]);
    }

    /**
     * Get movie details by ID
     */
    public function getMovieDetails($movieId)
    {
        return $this->makeRequest("/movie/{$movieId}", ['append_to_response' => 'credits,videos,images']);
    }

    /**
     * Search movies
     */
    public function searchMovies($query, $page = 1)
    {
        return $this->makeRequest("/search/movie", [
            'query' => $query,
            'page' => $page
        ]);
    }

    /**
     * Get movie genres
     */
    public function getMovieGenres()
    {
        return Cache::remember('tmdb_movie_genres', 86400, function () {
            $response = $this->makeRequest("/genre/movie/list");
            return $response['genres'] ?? [];
        });
    }

    /**
     * Get genres (alias for getMovieGenres)
     */
    public function getGenres()
    {
        return $this->getMovieGenres();
    }

    /**
     * Get countries
     */
    public function getCountries()
    {
        return Cache::remember('tmdb_countries', 86400, function () {
            $response = $this->makeRequest("/configuration/countries");
            return $response ?? [];
        });
    }

    /**
     * Get full image URL
     */
    public function getImageUrl($path, $size = 'w500')
    {
        if (!$path) {
            return null;
        }
        return "{$this->imageUrl}/{$size}{$path}";
    }

    /**
     * Get backdrop image URL
     */
    public function getBackdropUrl($path, $size = 'original')
    {
        if (!$path) {
            return null;
        }
        return "{$this->imageUrl}/{$size}{$path}";
    }

    /**
     * Make API request to TMDB
     */
    protected function makeRequest($endpoint, $params = [])
    {
        $url = $this->baseUrl . $endpoint;
        $defaultParams = [
            'api_key' => $this->apiKey,
            'language' => 'en-US'
        ];

        $params = array_merge($defaultParams, $params);

        try {
            $response = Http::timeout(30)->get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            // Handle specific error cases
            if ($response->status() === 401) {
                throw new \Exception('Invalid TMDB API key');
            }

            if ($response->status() === 404) {
                throw new \Exception('Resource not found');
            }

            if ($response->status() === 429) {
                throw new \Exception('TMDB API rate limit exceeded');
            }

            throw new \Exception('TMDB API request failed: ' . $response->body());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception('Failed to connect to TMDB API: ' . $e->getMessage());
        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new \Exception('TMDB API request failed: ' . $e->getMessage());
        }
    }

    /**
     * Format movie data for database storage
     */
    public function formatMovieData($movieData)
    {
        return [
            'tmdb_id' => $movieData['id'],
            'title' => $movieData['title'],
            'original_title' => $movieData['original_title'] ?? $movieData['title'],
            'overview' => $movieData['overview'] ?? '',
            'description' => $movieData['overview'] ?? '', // Also populate the description field
            'poster_path' => $movieData['poster_path'] ?? null,
            'backdrop_path' => $movieData['backdrop_path'] ?? null,
            'release_date' => $movieData['release_date'] ?? null,
            'runtime' => $movieData['runtime'] ?? null,
            'vote_average' => $movieData['vote_average'] ?? 0,
            'vote_count' => $movieData['vote_count'] ?? 0,
            'popularity' => $movieData['popularity'] ?? 0,
            'adult' => $movieData['adult'] ?? false,
            'original_language' => $movieData['original_language'] ?? 'en',
            'genre_ids' => isset($movieData['genre_ids']) ? json_encode($movieData['genre_ids']) : null,
            'status' => $movieData['status'] ?? 'Released',
            'tagline' => $movieData['tagline'] ?? '',
        ];
    }

    /**
     * Import genres from TMDB data
     */
    public function importGenres($movieData)
    {
        $genres = [];

        // Check if we have genre_ids array and fetch genre details if needed
        if (isset($movieData['genre_ids']) && is_array($movieData['genre_ids']) && empty($movieData['genres'])) {
            // If we only have genre IDs but not full genre data, fetch all genres and match
            $allGenres = $this->getMovieGenres();
            if (isset($allGenres['genres']) && is_array($allGenres['genres'])) {
                $genreMap = [];
                foreach ($allGenres['genres'] as $genre) {
                    $genreMap[$genre['id']] = $genre['name'];
                }

                foreach ($movieData['genre_ids'] as $genreId) {
                    if (isset($genreMap[$genreId])) {
                        $genre = \App\Models\Genre::firstOrCreate(
                            ['tmdb_id' => $genreId],
                            ['name' => $genreMap[$genreId]]
                        );
                        $genres[] = $genre->id;
                    }
                }
            }
        }
        // Check if we have full genres data
        elseif (isset($movieData['genres']) && is_array($movieData['genres'])) {
            foreach ($movieData['genres'] as $genreData) {
                if (isset($genreData['id']) && isset($genreData['name'])) {
                    $genre = \App\Models\Genre::firstOrCreate(
                        ['tmdb_id' => $genreData['id']],
                        ['name' => $genreData['name']]
                    );
                    $genres[] = $genre->id;
                }
            }
        }

        // Log for debugging
        if (empty($genres)) {
            Log::info('No genres found for movie', [
                'movie_id' => $movieData['id'] ?? 'unknown',
                'has_genres' => isset($movieData['genres']),
                'has_genre_ids' => isset($movieData['genre_ids']),
                'genres_data' => $movieData['genres'] ?? null,
                'genre_ids_data' => $movieData['genre_ids'] ?? null
            ]);
        }

        return $genres;
    }

    /**
     * Import production countries from TMDB data
     */
    public function importCountries($movieData)
    {
        $countries = [];

        if (isset($movieData['production_countries']) && is_array($movieData['production_countries'])) {
            foreach ($movieData['production_countries'] as $countryData) {
                // Skip if required fields are missing
                if (!isset($countryData['iso_3166_1'])) {
                    continue;
                }

                $country = \App\Models\Country::firstOrCreate(
                    ['iso_3166_1' => $countryData['iso_3166_1']],
                    [
                        'english_name' => $countryData['english_name'] ?? $countryData['name'] ?? $countryData['iso_3166_1'],
                        'native_name' => $countryData['native_name'] ?? null
                    ]
                );
                $countries[] = $country->id;
            }
        }

        return $countries;
    }

    /**
     * Import actors/credits from TMDB data
     */
    public function importActors($movieData)
    {
        $actors = [];

        if (isset($movieData['credits']['cast']) && is_array($movieData['credits']['cast'])) {
            foreach ($movieData['credits']['cast'] as $castData) {
                $actor = \App\Models\Actor::firstOrCreate(
                    ['tmdb_id' => $castData['id']],
                    [
                        'name' => $castData['name'],
                        'profile_path' => $castData['profile_path'] ?? null,
                        'gender' => (string)($castData['gender'] ?? 0),
                        'popularity' => $castData['popularity'] ?? 0
                    ]
                );

                $actors[] = [
                    'actor_id' => $actor->id,
                    'character' => $castData['character'] ?? null,
                    'order' => $castData['order'] ?? 0,
                    'profile_path' => $castData['profile_path'] ?? null
                ];
            }
        }

        return $actors;
    }

    /**
     * Download and save actor profile image to local storage
     */
    public function downloadAndSaveActorProfile($profilePath, $actorId)
    {
        if (!$profilePath) {
            return null;
        }

        try {
            $profileUrl = $this->getImageUrl($profilePath, 'w185');
            $client = new Client();
            $response = $client->get($profileUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getBody()->getContents();
                $extension = pathinfo($profilePath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'actor_' . $actorId . '_' . \Illuminate\Support\Str::random(10) . '.' . $extension;
                $filePath = 'actors/profiles/' . $fileName;

                Storage::disk('public')->put($filePath, $imageContent);

                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download actor profile: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download and save movie poster to local storage
     */
    public function downloadAndSavePoster($posterPath, $movieId)
    {
        if (!$posterPath) {
            return null;
        }

        try {
            $posterUrl = $this->getImageUrl($posterPath, 'w500');
            $client = new Client();
            $response = $client->get($posterUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getBody()->getContents();
                $extension = pathinfo($posterPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'poster_' . $movieId . '_' . Str::random(10) . '.' . $extension;
                $filePath = 'movies/posters/' . $fileName;

                Storage::disk('public')->put($filePath, $imageContent);

                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download poster: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download and save movie backdrop to local storage
     */
    public function downloadAndSaveBackdrop($backdropPath, $movieId)
    {
        if (!$backdropPath) {
            return null;
        }

        try {
            $backdropUrl = $this->getBackdropUrl($backdropPath, 'w1280');
            $client = new Client();
            $response = $client->get($backdropUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getBody()->getContents();
                $extension = pathinfo($backdropPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'backdrop_' . $movieId . '_' . Str::random(10) . '.' . $extension;
                $filePath = 'movies/backdrops/' . $fileName;

                Storage::disk('public')->put($filePath, $imageContent);

                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download backdrop: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get local poster URL
     */
    public function getLocalPosterUrl($localPath)
    {
        if (!$localPath) {
            return null;
        }
        return Storage::url($localPath);
    }

    /**
     * Get local backdrop URL
     */
    public function getLocalBackdropUrl($localPath)
    {
        if (!$localPath) {
            return null;
        }
        return Storage::url($localPath);
    }

    // TV Series Methods

    /**
     * Get popular TV series
     */
    public function getPopularTv($page = 1)
    {
        return $this->makeRequest("/tv/popular", ['page' => $page]);
    }

    /**
     * Get top rated TV series
     */
    public function getTopRatedTv($page = 1)
    {
        return $this->makeRequest("/tv/top_rated", ['page' => $page]);
    }

    /**
     * Get TV series airing today
     */
    public function getAiringTodayTv($page = 1)
    {
        return $this->makeRequest("/tv/airing_today", ['page' => $page]);
    }

    /**
     * Get TV series on the air
     */
    public function getOnTheAirTv($page = 1)
    {
        return $this->makeRequest("/tv/on_the_air", ['page' => $page]);
    }

    /**
     * Get TV series details by ID
     */
    public function getTvDetails($tvId)
    {
        return $this->makeRequest("/tv/{$tvId}", ['append_to_response' => 'credits,videos,images,external_ids']);
    }

    /**
     * Search TV series
     */
    public function searchTv($query, $page = 1)
    {
        return $this->makeRequest("/search/tv", [
            'query' => $query,
            'page' => $page
        ]);
    }

    /**
     * Get TV series genres
     */
    public function getTvGenres()
    {
        return Cache::remember('tmdb_tv_genres', 86400, function () {
            return $this->makeRequest("/genre/tv/list");
        });
    }

    /**
     * Format TV series data for database storage
     */
    public function formatTvData($tvData)
    {
        // Generate slug from title
        $slug = Str::slug($tvData['name']);
        $originalSlug = $slug;
        $counter = 1;
        
        // Ensure slug is unique
        while (\App\Models\Tv::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return [
            'tmdb_id' => $tvData['id'],
            'title' => $tvData['name'],
            'slug' => $slug,
            'original_title' => $tvData['original_name'] ?? $tvData['name'],
            'overview' => $tvData['overview'] ?? '',
            'description' => $tvData['overview'] ?? '',
            'poster_path' => $tvData['poster_path'] ?? null,
            'backdrop_path' => $tvData['backdrop_path'] ?? null,
            'first_air_date' => $tvData['first_air_date'] ?? null,
            'last_air_date' => $tvData['last_air_date'] ?? null,
            'episode_run_time' => isset($tvData['episode_run_time']) && !empty($tvData['episode_run_time']) ? $tvData['episode_run_time'][0] : null,
            'number_of_seasons' => $tvData['number_of_seasons'] ?? 0,
            'number_of_episodes' => $tvData['number_of_episodes'] ?? 0,
            'vote_average' => $tvData['vote_average'] ?? 0,
            'vote_count' => $tvData['vote_count'] ?? 0,
            'popularity' => $tvData['popularity'] ?? 0,
            'original_language' => $tvData['original_language'] ?? 'en',
            'status' => $tvData['status'] ?? 'Released',
            'tagline' => $tvData['tagline'] ?? '',
            'type' => $tvData['type'] ?? 'Scripted',
            'genres' => isset($tvData['genres']) ? json_encode($tvData['genres']) : null,
            'homepage' => $tvData['homepage'] ?? null,
            'in_production' => $tvData['in_production'] ?? false,
        ];
    }

    /**
     * Import TV series genres from TMDB data
     */
    public function importTvGenres($tvData)
    {
        $genres = [];

        // Check if we have genre_ids array and fetch genre details if needed
        if (isset($tvData['genre_ids']) && is_array($tvData['genre_ids']) && empty($tvData['genres'])) {
            // If we only have genre IDs but not full genre data, fetch all genres and match
            $allGenres = $this->getTvGenres();
            if (isset($allGenres['genres']) && is_array($allGenres['genres'])) {
                $genreMap = [];
                foreach ($allGenres['genres'] as $genre) {
                    $genreMap[$genre['id']] = $genre['name'];
                }

                foreach ($tvData['genre_ids'] as $genreId) {
                    if (isset($genreMap[$genreId])) {
                        $genre = \App\Models\Genre::firstOrCreate(
                            ['tmdb_id' => $genreId],
                            ['name' => $genreMap[$genreId]]
                        );
                        $genres[] = $genre->id;
                    }
                }
            }
        }
        // Check if we have full genres data
        elseif (isset($tvData['genres']) && is_array($tvData['genres'])) {
            foreach ($tvData['genres'] as $genreData) {
                if (isset($genreData['id']) && isset($genreData['name'])) {
                    $genre = \App\Models\Genre::firstOrCreate(
                        ['tmdb_id' => $genreData['id']],
                        ['name' => $genreData['name']]
                    );
                    $genres[] = $genre->id;
                }
            }
        }

        // Log for debugging
        if (empty($genres)) {
            Log::info('No genres found for TV series', [
                'tv_id' => $tvData['id'] ?? 'unknown',
                'has_genres' => isset($tvData['genres']),
                'has_genre_ids' => isset($tvData['genre_ids']),
                'genres_data' => $tvData['genres'] ?? null,
                'genre_ids_data' => $tvData['genre_ids'] ?? null
            ]);
        }

        return $genres;
    }

    /**
     * Import TV series production countries from TMDB data
     */
    public function importTvCountries($tvData)
    {
        $countries = [];

        if (isset($tvData['production_countries']) && is_array($tvData['production_countries'])) {
            foreach ($tvData['production_countries'] as $countryData) {
                // Skip if required fields are missing
                if (!isset($countryData['iso_3166_1'])) {
                    continue;
                }

                $country = \App\Models\Country::firstOrCreate(
                    ['iso_3166_1' => $countryData['iso_3166_1']],
                    [
                        'english_name' => $countryData['english_name'] ?? $countryData['name'] ?? $countryData['iso_3166_1'],
                        'native_name' => $countryData['native_name'] ?? null
                    ]
                );
                $countries[] = $country->id;
            }
        }

        return $countries;
    }

    /**
     * Import TV series actors/credits from TMDB data
     */
    public function importTvActors($tvData)
    {
        $actors = [];

        if (isset($tvData['credits']['cast']) && is_array($tvData['credits']['cast'])) {
            foreach ($tvData['credits']['cast'] as $castData) {
                $actor = \App\Models\Actor::firstOrCreate(
                    ['tmdb_id' => $castData['id']],
                    [
                        'name' => $castData['name'],
                        'profile_path' => $castData['profile_path'] ?? null,
                        'gender' => (string)($castData['gender'] ?? 0),
                        'popularity' => $castData['popularity'] ?? 0
                    ]
                );

                $actors[] = [
                    'actor_id' => $actor->id,
                    'character' => $castData['character'] ?? null,
                    'order' => $castData['order'] ?? 0,
                    'profile_path' => $castData['profile_path'] ?? null
                ];
            }
        }

        return $actors;
    }

    /**
     * Download and save TV series poster to local storage
     */
    public function downloadAndSaveTvPoster($posterPath, $tvId)
    {
        if (!$posterPath) {
            return null;
        }

        try {
            $posterUrl = $this->getImageUrl($posterPath, 'w500');
            $client = new Client();
            $response = $client->get($posterUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getBody()->getContents();
                $extension = pathinfo($posterPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'tv_poster_' . $tvId . '_' . Str::random(10) . '.' . $extension;
                $filePath = 'tv/posters/' . $fileName;

                Storage::disk('public')->put($filePath, $imageContent);

                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download TV poster: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download and save TV series backdrop to local storage
     */
    public function downloadAndSaveTvBackdrop($backdropPath, $tvId)
    {
        if (!$backdropPath) {
            return null;
        }

        try {
            $backdropUrl = $this->getBackdropUrl($backdropPath, 'w1280');
            $client = new Client();
            $response = $client->get($backdropUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getBody()->getContents();
                $extension = pathinfo($backdropPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'tv_backdrop_' . $tvId . '_' . Str::random(10) . '.' . $extension;
                $filePath = 'tv/backdrops/' . $fileName;

                Storage::disk('public')->put($filePath, $imageContent);

                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download TV backdrop: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get TV series season details
     */
    public function getTvSeasonDetails($tvId, $seasonNumber)
    {
        return $this->makeRequest("/tv/{$tvId}/season/{$seasonNumber}");
    }

    /**
     * Get TV series episode details
     */
    public function getTvEpisodeDetails($tvId, $seasonNumber, $episodeNumber)
    {
        return $this->makeRequest("/tv/{$tvId}/season/{$seasonNumber}/episode/{$episodeNumber}");
    }

    /**
     * Get all seasons for a TV series
     */
    public function getTvSeasons($tvId)
    {
        $tvDetails = $this->getTvDetails($tvId);
        return $tvDetails['seasons'] ?? [];
    }

    /**
     * Download and save season poster to local storage
     */
    public function downloadAndSaveSeasonPoster($posterPath, $seasonId)
    {
        if (!$posterPath) {
            return null;
        }

        try {
            $posterUrl = $this->getImageUrl($posterPath, 'w500');
            $client = new Client();
            $response = $client->get($posterUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getBody()->getContents();
                $extension = pathinfo($posterPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'season_poster_' . $seasonId . '_' . Str::random(10) . '.' . $extension;
                $filePath = 'seasons/posters/' . $fileName;

                Storage::disk('public')->put($filePath, $imageContent);

                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download season poster: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download and save episode still to local storage
     */
    public function downloadAndSaveEpisodeStill($stillPath, $episodeId)
    {
        if (!$stillPath) {
            return null;
        }

        try {
            $stillUrl = $this->getImageUrl($stillPath, 'w300');
            $client = new Client();
            $response = $client->get($stillUrl);

            if ($response->getStatusCode() === 200) {
                $imageContent = $response->getBody()->getContents();
                $extension = pathinfo($stillPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = 'episode_still_' . $episodeId . '_' . Str::random(10) . '.' . $extension;
                $filePath = 'episodes/stills/' . $fileName;

                Storage::disk('public')->put($filePath, $imageContent);

                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to download episode still: ' . $e->getMessage());
            return null;
        }
    }
}

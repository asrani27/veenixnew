<?php

namespace App\Http\Controllers;

use App\Models\Tv;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TvController extends Controller
{
    protected $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function index()
    {
        $data = Tv::with(['genres_data', 'countries'])->orderBy('id', 'DESC')->paginate(10);
        return view('admin.tv.index', compact('data'));
    }

    public function create()
    {
        return view('admin.tv.create');
    }

    /**
     * Show TMDB TV series search page
     */
    public function searchTmdb()
    {
        return view('admin.tv.search-tmdb');
    }

    /**
     * Search TV series from TMDB API
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
            $response = $this->tmdbService->searchTv($query, $page);

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
     * Show popular TV series from TMDB
     */
    public function popularTmdb(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $type = $request->input('type', 'popular'); // popular, top_rated, airing_today, on_the_air

            // Debug logging
            Log::info('popularTmdb called', [
                'page' => $page,
                'type' => $type,
                'api_key_present' => !empty(env('TMDB_API_KEY'))
            ]);

            switch ($type) {
                case 'top_rated':
                    $response = $this->tmdbService->getTopRatedTv($page);
                    break;
                case 'airing_today':
                    $response = $this->tmdbService->getAiringTodayTv($page);
                    break;
                case 'on_the_air':
                    $response = $this->tmdbService->getOnTheAirTv($page);
                    break;
                default:
                    $response = $this->tmdbService->getPopularTv($page);
            }

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('popularTmdb error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import TV series from TMDB to database
     */
    public function importFromTmdb($tmdbId)
    {
        try {
            // Check if TV series already exists
            $existingTv = Tv::where('tmdb_id', $tmdbId)->first();
            if ($existingTv) {
                return response()->json([
                    'success' => false,
                    'message' => 'TV series already exists in database'
                ], 400);
            }

            // Get TV series details from TMDB
            $tvData = $this->tmdbService->getTvDetails($tmdbId);
            $formattedData = $this->tmdbService->formatTvData($tvData);

            // Download and save poster and backdrop
            $localPosterPath = $this->tmdbService->downloadAndSaveTvPoster($tvData['poster_path'], $tmdbId);
            $localBackdropPath = $this->tmdbService->downloadAndSaveTvBackdrop($tvData['backdrop_path'], $tmdbId);

            // Add local paths to data
            $formattedData['local_poster_path'] = $localPosterPath;
            $formattedData['local_backdrop_path'] = $localBackdropPath;

            // Create TV series record
            $tv = Tv::create($formattedData);

            // Import genres
            $genreIds = $this->tmdbService->importTvGenres($tvData);
            if (!empty($genreIds)) {
                $tv->genres_data()->attach($genreIds);
            }

            // Import production countries
            $countryIds = $this->tmdbService->importTvCountries($tvData);
            if (!empty($countryIds)) {
                $tv->countries()->attach($countryIds);
            }

            // Import actors
            $actorsData = $this->tmdbService->importTvActors($tvData);
            if (!empty($actorsData)) {
                $tv->actors()->attach($actorsData);
            }

            return response()->json([
                'success' => true,
                'message' => 'TV series imported successfully with genres, countries, and actors',
                'data' => $tv
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get TV series details from TMDB
     */
    public function getTmdbDetails($tmdbId)
    {
        try {
            $tvData = $this->tmdbService->getTvDetails($tmdbId);

            // Add image URLs
            $tvData['poster_url'] = $this->tmdbService->getImageUrl($tvData['poster_path']);
            $tvData['backdrop_url'] = $this->tmdbService->getBackdropUrl($tvData['backdrop_path']);

            return response()->json([
                'success' => true,
                'data' => $tvData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk import TV series from TMDB
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
                // Check if TV series already exists
                $existingTv = Tv::where('tmdb_id', $tmdbId)->first();
                if ($existingTv) {
                    $skipped[] = $tmdbId;
                    continue;
                }

                // Get TV series details from TMDB
                $tvData = $this->tmdbService->getTvDetails($tmdbId);
                $formattedData = $this->tmdbService->formatTvData($tvData);

                // Download and save poster and backdrop
                $localPosterPath = $this->tmdbService->downloadAndSaveTvPoster($tvData['poster_path'], $tmdbId);
                $localBackdropPath = $this->tmdbService->downloadAndSaveTvBackdrop($tvData['backdrop_path'], $tmdbId);

                // Add local paths to data
                $formattedData['local_poster_path'] = $localPosterPath;
                $formattedData['local_backdrop_path'] = $localBackdropPath;

                // Create TV series record
                $tv = Tv::create($formattedData);

                // Import genres
                $genreIds = $this->tmdbService->importTvGenres($tvData);
                if (!empty($genreIds)) {
                    $tv->genres_data()->attach($genreIds);
                }

                // Import production countries
                $countryIds = $this->tmdbService->importTvCountries($tvData);
                if (!empty($countryIds)) {
                    $tv->countries()->attach($countryIds);
                }

                // Import actors
                $actorsData = $this->tmdbService->importTvActors($tvData);
                if (!empty($actorsData)) {
                    $tv->actors()->attach($actorsData);
                }

                $imported[] = $tv;
            }

            return response()->json([
                'success' => true,
                'message' => "Imported " . count($imported) . " TV series with genres, countries, and actors, skipped " . count($skipped) . " TV series",
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
     * Show the form for editing the specified TV series.
     */
    public function edit($id)
    {
        $tv = Tv::findOrFail($id);
        return view('admin.tv.edit', compact('tv'));
    }

    /**
     * Update the specified TV series in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'original_title' => 'nullable|string|max:255',
                'overview' => 'nullable|string',
                'description' => 'nullable|string',
                'poster_path' => 'nullable|string|max:255',
                'backdrop_path' => 'nullable|string|max:255',
                'first_air_date' => 'nullable|date',
                'last_air_date' => 'nullable|date',
                'episode_run_time' => 'nullable|integer|min:0',
                'number_of_seasons' => 'nullable|integer|min:0',
                'number_of_episodes' => 'nullable|integer|min:0',
                'vote_average' => 'nullable|numeric|min:0|max:10',
                'vote_count' => 'nullable|integer|min:0',
                'popularity' => 'nullable|numeric|min:0',
                'original_language' => 'nullable|string|max:10',
                'status' => 'nullable|string|max:50',
                'tagline' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:50',
                'homepage' => 'nullable|url|max:255',
                'in_production' => 'nullable|boolean',
                'local_poster_path' => 'nullable|string|max:255',
                'local_backdrop_path' => 'nullable|string|max:255',
                'genres' => 'nullable|array',
                'genres.*' => 'exists:genres,id',
                'countries' => 'nullable|array',
                'countries.*' => 'exists:countries,id',
                'actors' => 'nullable|array',
                'actors.*' => 'exists:actors,id',
            ]);

            $tv = Tv::findOrFail($id);

            // Update TV series data
            $tv->title = $request->title;
            $tv->original_title = $request->original_title;
            $tv->overview = $request->overview;
            $tv->description = $request->description;
            $tv->poster_path = $request->poster_path;
            $tv->backdrop_path = $request->backdrop_path;
            $tv->first_air_date = $request->first_air_date;
            $tv->last_air_date = $request->last_air_date;
            $tv->episode_run_time = $request->episode_run_time;
            $tv->number_of_seasons = $request->number_of_seasons;
            $tv->number_of_episodes = $request->number_of_episodes;
            $tv->vote_average = $request->vote_average;
            $tv->vote_count = $request->vote_count;
            $tv->popularity = $request->popularity;
            $tv->original_language = $request->original_language;
            $tv->status = $request->status;
            $tv->tagline = $request->tagline;
            $tv->type = $request->type;
            $tv->homepage = $request->homepage;
            $tv->in_production = $request->in_production;
            $tv->local_poster_path = $request->local_poster_path;
            $tv->local_backdrop_path = $request->local_backdrop_path;

            $tv->save();

            // Sync relationships
            if ($request->has('genres')) {
                $tv->genres_data()->sync($request->genres);
            }

            if ($request->has('countries')) {
                $tv->countries()->sync($request->countries);
            }

            if ($request->has('actors')) {
                $tv->actors()->sync($request->actors);
            }

            return redirect()->route('admin.tv.seasons-page', $tv->id)->with('success', 'TV series updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update TV series: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified TV series from storage.
     */
    public function destroy($id)
    {
        try {
            $tv = Tv::findOrFail($id);

            // Delete local poster if exists
            if ($tv->local_poster_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($tv->local_poster_path);
            }

            // Delete local backdrop if exists
            if ($tv->local_backdrop_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($tv->local_backdrop_path);
            }

            // Delete TV series record
            $tv->delete();

            return redirect()->route('admin.tv.index')->with('success', 'TV series deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.index')->with('error', 'Failed to delete TV series: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified TV series.
     */
    public function show($slug)
    {
        try {
            $tv = Tv::with(['genres_data', 'countries', 'actors', 'seasons.episodes'])
                ->where('slug', $slug)
                ->firstOrFail();

            // Get related TV series
            $relatedTvs = Tv::with(['genres_data'])
                ->where('id', '!=', $tv->id)
                ->whereHas('genres_data', function ($query) use ($tv) {
                    $query->whereIn('genres.id', $tv->genres_data->pluck('id'));
                })
                ->inRandomOrder()
                ->take(6)
                ->get();

            return view('tv', compact('tv', 'relatedTvs'));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    /**
     * Display the specified TV series with episode information.
     */
    public function showEpisode($slug, $season_number, $episode_number)
    {
        try {
            $tv = Tv::with(['genres_data', 'countries', 'actors', 'seasons.episodes'])
                ->where('slug', $slug)
                ->firstOrFail();

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

            // Get related TV series
            $relatedTvs = Tv::with(['genres_data'])
                ->where('id', '!=', $tv->id)
                ->whereHas('genres_data', function ($query) use ($tv) {
                    $query->whereIn('genres.id', $tv->genres_data->pluck('id'));
                })
                ->inRandomOrder()
                ->take(6)
                ->get();

            return view('tv', compact('tv', 'relatedTvs', 'season', 'episode', 'season_number', 'episode_number'));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    /**
     * Get TV series seasons
     */
    public function getSeasons($tvId)
    {
        try {
            $tv = Tv::findOrFail($tvId);
            $seasons = $this->tmdbService->getTvSeasons($tv->tmdb_id);

            return response()->json([
                'success' => true,
                'data' => $seasons
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show TV series seasons page
     */
    public function seasonsPage($tvId)
    {
        try {
            $tv = Tv::with(['genres_data', 'countries'])->findOrFail($tvId);
            $seasons = \App\Models\Season::where('tv_id', $tvId)->orderBy('season_number')->get();

            return view('admin.tv.seasons', compact('tv', 'seasons'));
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.index')->with('error', 'TV series not found.');
        }
    }

    /**
     * Get TV series season details
     */
    public function getSeasonDetails($tvId, $seasonNumber)
    {
        try {
            $tv = Tv::findOrFail($tvId);
            $seasonDetails = $this->tmdbService->getTvSeasonDetails($tv->tmdb_id, $seasonNumber);

            return response()->json([
                'success' => true,
                'data' => $seasonDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show TV series episodes page
     */
    public function getEpisodes($tvId, $seasonNumber)
    {
        try {
            $tv = Tv::with(['genres_data', 'countries'])->findOrFail($tvId);
            $season = \App\Models\Season::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->firstOrFail();
            $episodes = \App\Models\Episode::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->orderBy('episode_number')
                ->get();

            return view('admin.tv.episodes', compact('tv', 'season', 'episodes'));
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.seasons-page', $tvId)->with('error', 'Season not found.');
        }
    }

    /**
     * Get TV series episode details
     */
    public function getEpisodeDetails($tvId, $seasonNumber, $episodeNumber)
    {
        try {
            $tv = Tv::findOrFail($tvId);
            $episodeDetails = $this->tmdbService->getTvEpisodeDetails($tv->tmdb_id, $seasonNumber, $episodeNumber);

            return response()->json([
                'success' => true,
                'data' => $episodeDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import seasons and episodes for TV series
     */
    public function importSeasonsAndEpisodes($tvId)
    {
        try {
            $tv = Tv::findOrFail($tvId);
            $seasonsData = $this->tmdbService->getTvSeasons($tv->tmdb_id);

            $importedSeasons = [];
            $importedEpisodes = [];

            foreach ($seasonsData as $seasonData) {
                // Skip special seasons if needed
                if ($seasonData['season_number'] === 0) {
                    continue;
                }

                // Import season
                $season = \App\Models\Season::updateOrCreate(
                    [
                        'tv_id' => $tv->id,
                        'season_number' => $seasonData['season_number']
                    ],
                    [
                        'tmdb_id' => $seasonData['id'],
                        'name' => $seasonData['name'],
                        'overview' => $seasonData['overview'] ?? '',
                        'poster_path' => $seasonData['poster_path'] ?? null,
                        'air_date' => $seasonData['air_date'] ?? null,
                        'episode_count' => $seasonData['episode_count'] ?? 0,
                        'vote_average' => $seasonData['vote_average'] ?? 0,
                        'vote_count' => $seasonData['vote_count'] ?? 0,
                    ]
                );

                // Download season poster
                if ($seasonData['poster_path']) {
                    $localPosterPath = $this->tmdbService->downloadAndSaveSeasonPoster($seasonData['poster_path'], $season->id);
                    $season->local_poster_path = $localPosterPath;
                    $season->save();
                }

                $importedSeasons[] = $season;

                // Get season details with episodes
                $seasonDetails = $this->tmdbService->getTvSeasonDetails($tv->tmdb_id, $seasonData['season_number']);

                if (isset($seasonDetails['episodes'])) {
                    foreach ($seasonDetails['episodes'] as $episodeData) {
                        $episode = \App\Models\Episode::updateOrCreate(
                            [
                                'tv_id' => $tv->id,
                                'season_id' => $season->id,
                                'season_number' => $seasonData['season_number'],
                                'episode_number' => $episodeData['episode_number']
                            ],
                            [
                                'tmdb_id' => $episodeData['id'],
                                'name' => $episodeData['name'],
                                'overview' => $episodeData['overview'] ?? '',
                                'still_path' => $episodeData['still_path'] ?? null,
                                'air_date' => $episodeData['air_date'] ?? null,
                                'vote_average' => $episodeData['vote_average'] ?? 0,
                                'vote_count' => $episodeData['vote_count'] ?? 0,
                                'runtime' => $episodeData['runtime'] ?? null,
                                'publish' => false,
                            ]
                        );

                        // Download episode still
                        if ($episodeData['still_path']) {
                            $localStillPath = $this->tmdbService->downloadAndSaveEpisodeStill($episodeData['still_path'], $episode->id);
                            $episode->local_still_path = $localStillPath;
                            $episode->save();
                        }

                        $importedEpisodes[] = $episode;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Imported " . count($importedSeasons) . " seasons and " . count($importedEpisodes) . " episodes",
                'data' => [
                    'seasons' => $importedSeasons,
                    'episodes' => $importedEpisodes
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
     * Show the form for editing the specified season.
     */
    public function editSeason($tvId, $seasonNumber)
    {
        try {
            $tv = Tv::findOrFail($tvId);
            $season = \App\Models\Season::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->firstOrFail();

            return view('admin.tv.season-edit', compact('tv', 'season'));
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.seasons-page', $tvId)->with('error', 'Season not found.');
        }
    }

    /**
     * Update the specified season in storage.
     */
    public function updateSeason(Request $request, $tvId, $seasonNumber)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'overview' => 'nullable|string',
                'air_date' => 'nullable|date',
                'episode_count' => 'nullable|integer|min:0',
                'vote_average' => 'nullable|numeric|min:0|max:10',
                'vote_count' => 'nullable|integer|min:0',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $tv = Tv::findOrFail($tvId);
            $season = \App\Models\Season::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->firstOrFail();

            // Update season data
            $season->name = $request->name;
            $season->overview = $request->overview;
            $season->air_date = $request->air_date;
            $season->episode_count = $request->episode_count;
            $season->vote_average = $request->vote_average;
            $season->vote_count = $request->vote_count;

            // Handle poster upload
            if ($request->hasFile('poster')) {
                // Delete old poster if exists
                if ($season->local_poster_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($season->local_poster_path);
                }

                $posterPath = $request->file('poster')->store('seasons/posters', 'public');
                $season->local_poster_path = $posterPath;
            }

            $season->save();

            return redirect()->route('admin.tv.seasons-page', $tvId)->with('success', 'Season updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update season: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified episode.
     */
    public function editEpisode($tvId, $seasonNumber, $episodeNumber)
    {
        try {
            $tv = Tv::findOrFail($tvId);
            $season = \App\Models\Season::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->firstOrFail();
            $episode = \App\Models\Episode::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->where('episode_number', $episodeNumber)
                ->firstOrFail();

            return view('admin.tv.episode-edit', compact('tv', 'season', 'episode'));
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.seasons-page', $tvId)->with('error', 'Episode not found.');
        }
    }

    /**
     * Update the specified episode in storage.
     */
    public function updateEpisode(Request $request, $tvId, $seasonNumber, $episodeNumber)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'overview' => 'nullable|string',
                'air_date' => 'nullable|date',
                'runtime' => 'nullable|integer|min:0',
                'vote_average' => 'nullable|numeric|min:0|max:10',
                'vote_count' => 'nullable|integer|min:0',
                'still' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $tv = Tv::findOrFail($tvId);
            $episode = \App\Models\Episode::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->where('episode_number', $episodeNumber)
                ->firstOrFail();

            // Update episode data
            $episode->name = $request->name;
            $episode->overview = $request->overview;
            $episode->air_date = $request->air_date;
            $episode->runtime = $request->runtime;
            $episode->vote_average = $request->vote_average;
            $episode->vote_count = $request->vote_count;

            // Handle still image upload
            if ($request->hasFile('still')) {
                // Delete old still if exists
                if ($episode->local_still_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($episode->local_still_path);
                }

                $stillPath = $request->file('still')->store('episodes/stills', 'public');
                $episode->local_still_path = $stillPath;
            }

            $episode->save();

            return redirect()->route('admin.tv.season.episodes', ['tv' => $tvId, 'season' => $seasonNumber])->with('success', 'Episode updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update episode: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show TV series episodes page
     */
    public function episodesPage($tvId, $seasonNumber)
    {
        try {
            $tv = Tv::with(['genres_data', 'countries'])->findOrFail($tvId);
            $season = \App\Models\Season::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->firstOrFail();
            $episodes = \App\Models\Episode::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->orderBy('episode_number')
                ->get();

            return view('admin.tv.episodes', compact('tv', 'season', 'episodes'));
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.seasons-page', $tvId)->with('error', 'Season not found.');
        }
    }

    /**
     * Show the form for creating a new episode.
     */
    public function createEpisode($tvId, $seasonNumber)
    {
        try {
            $tv = Tv::findOrFail($tvId);
            $season = \App\Models\Season::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->firstOrFail();

            return view('admin.tv.episode-create', compact('tv', 'season'));
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.seasons-page', $tvId)->with('error', 'Season not found.');
        }
    }

    /**
     * Import a specific episode from TMDB
     */
    public function importEpisode(Request $request, $tvId, $seasonNumber)
    {
        $request->validate([
            'episode_number' => 'required|integer|min:1'
        ]);

        try {
            $tv = Tv::findOrFail($tvId);
            $season = \App\Models\Season::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->firstOrFail();

            $episodeNumber = $request->episode_number;

            // Check if episode already exists
            $existingEpisode = \App\Models\Episode::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->where('episode_number', $episodeNumber)
                ->first();

            if ($existingEpisode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Episode ' . $episodeNumber . ' already exists in this season.'
                ], 400);
            }

            // Get episode details from TMDB
            $episodeData = $this->tmdbService->getTvEpisodeDetails($tv->tmdb_id, $seasonNumber, $episodeNumber);

            // Create new episode
            $episode = \App\Models\Episode::create([
                'tv_id' => $tvId,
                'season_id' => $season->id,
                'season_number' => $seasonNumber,
                'episode_number' => $episodeNumber,
                'tmdb_id' => $episodeData['id'],
                'name' => $episodeData['name'],
                'overview' => $episodeData['overview'] ?? '',
                'still_path' => $episodeData['still_path'] ?? null,
                'air_date' => $episodeData['air_date'] ?? null,
                'vote_average' => $episodeData['vote_average'] ?? 0,
                'vote_count' => $episodeData['vote_count'] ?? 0,
                'runtime' => $episodeData['runtime'] ?? null,
                'publish' => false,
            ]);

            // Download episode still
            if ($episodeData['still_path']) {
                $localStillPath = $this->tmdbService->downloadAndSaveEpisodeStill($episodeData['still_path'], $episode->id);
                $episode->local_still_path = $localStillPath;
                $episode->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Episode ' . $episodeNumber . ' imported successfully from TMDB',
                'data' => [
                    'episode' => $episode,
                    'redirect_url' => route('admin.tv.episodes-page', ['tv' => $tvId, 'season' => $seasonNumber])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import episode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created episode in storage.
     */
    public function storeEpisode(Request $request, $tvId, $seasonNumber)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'overview' => 'nullable|string',
                'air_date' => 'nullable|date',
                'runtime' => 'nullable|integer|min:0',
                'vote_average' => 'nullable|numeric|min:0|max:10',
                'vote_count' => 'nullable|integer|min:0',
                'still' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $tv = Tv::findOrFail($tvId);
            $season = \App\Models\Season::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->firstOrFail();

            // Get the next episode number
            $lastEpisode = \App\Models\Episode::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->orderBy('episode_number', 'desc')
                ->first();

            $nextEpisodeNumber = $lastEpisode ? $lastEpisode->episode_number + 1 : 1;

            // Create new episode
            $episode = \App\Models\Episode::create([
                'tv_id' => $tvId,
                'season_id' => $season->id,
                'season_number' => $seasonNumber,
                'episode_number' => $nextEpisodeNumber,
                'name' => $request->name,
                'overview' => $request->overview,
                'air_date' => $request->air_date,
                'runtime' => $request->runtime,
                'vote_average' => $request->vote_average,
                'vote_count' => $request->vote_count,
                'publish' => false,
            ]);

            // Handle still image upload
            if ($request->hasFile('still')) {
                $stillPath = $request->file('still')->store('episodes/stills', 'public');
                $episode->local_still_path = $stillPath;
                $episode->save();
            }

            return redirect()->route('admin.tv.episodes-page', ['tv' => $tvId, 'season' => $seasonNumber])->with('success', 'Episode created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create episode: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified episode from storage.
     */
    public function destroyEpisode($tvId, $seasonNumber, $episodeNumber)
    {
        try {
            $tv = Tv::findOrFail($tvId);
            $episode = \App\Models\Episode::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->where('episode_number', $episodeNumber)
                ->firstOrFail();

            // Delete local still if exists
            if ($episode->local_still_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($episode->local_still_path);
            }

            // Delete episode folder from Wasabi
            $this->deleteEpisodeFolderFromWasabi($episode);

            // Delete local video file if exists
            if ($episode->file) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($episode->file);
            }

            // Delete episode record
            $episode->delete();

            return redirect()->route('admin.tv.episodes-page', ['tv' => $tvId, 'season' => $seasonNumber])->with('success', 'Episode deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.episodes-page', ['tv' => $tvId, 'season' => $seasonNumber])->with('error', 'Failed to delete episode: ' . $e->getMessage());
        }
    }

    /**
     * Delete episode folder from Wasabi storage
     */
    private function deleteEpisodeFolderFromWasabi($episode)
    {
        try {
            // Get TV show slug for folder structure
            $tvShow = $episode->tv;
            $tvSlug = $tvShow ? $tvShow->slug : 'unknown-tv';
            
            // Create folder path: tv-slug/season-xx/episode-xx/
            $folderPath = sprintf('%s/season-%02d/episode-%02d', 
                $tvSlug, 
                $episode->season_number, 
                $episode->episode_number
            );

            // Delete all files in the episode folder from Wasabi
            $wasabi = \Illuminate\Support\Facades\Storage::disk('wasabi');
            $files = $wasabi->allFiles($folderPath);
            
            foreach ($files as $file) {
                $wasabi->delete($file);
                \Illuminate\Support\Facades\Log::info('Deleted file from Wasabi', [
                    'file' => $file,
                    'episode_id' => $episode->id
                ]);
            }

            // Try to delete the empty directories (this might not always work depending on S3 implementation)
            try {
                // Delete episode directory
                if ($wasabi->exists($folderPath)) {
                    $wasabi->deleteDirectory($folderPath);
                }
                
                // Delete season directory if it's empty
                $seasonPath = dirname($folderPath);
                if ($wasabi->exists($seasonPath) && empty($wasabi->allFiles($seasonPath))) {
                    $wasabi->deleteDirectory($seasonPath);
                }
                
                // Delete tv show directory if it's empty
                $tvPath = dirname($seasonPath);
                if ($wasabi->exists($tvPath) && empty($wasabi->allFiles($tvPath))) {
                    $wasabi->deleteDirectory($tvPath);
                }
            } catch (\Exception $dirException) {
                // Directory deletion might fail if S3 doesn't support it, but files are already deleted
                \Illuminate\Support\Facades\Log::warning('Could not delete empty directories from Wasabi', [
                    'error' => $dirException->getMessage(),
                    'folder_path' => $folderPath
                ]);
            }

            \Illuminate\Support\Facades\Log::info('Successfully deleted episode folder from Wasabi', [
                'episode_id' => $episode->id,
                'folder_path' => $folderPath,
                'files_deleted' => count($files)
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to delete episode folder from Wasabi', [
                'episode_id' => $episode->id,
                'error' => $e->getMessage()
            ]);
            
            // Don't throw the exception to prevent episode deletion from failing
            // Just log the error and continue
        }
    }

    /**
     * Toggle publish status of an episode
     */
    public function togglePublishEpisode($tvId, $seasonNumber, $episodeNumber)
    {
        try {
            $episode = \App\Models\Episode::where('tv_id', $tvId)
                ->where('season_number', $seasonNumber)
                ->where('episode_number', $episodeNumber)
                ->firstOrFail();

            // Toggle the publish status
            $episode->publish = !$episode->publish;
            $episode->save();

            $status = $episode->publish ? 'published' : 'unpublished';

            return redirect()->route('admin.tv.episodes-page', ['tv' => $tvId, 'season' => $seasonNumber])
                ->with('success', "Episode {$episodeNumber} has been {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->route('admin.tv.episodes-page', ['tv' => $tvId, 'season' => $seasonNumber])
                ->with('error', 'Failed to toggle publish status: ' . $e->getMessage());
        }
    }
}

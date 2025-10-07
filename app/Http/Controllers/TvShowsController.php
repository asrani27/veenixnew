<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Tv;
use Illuminate\Http\Request;

class TvShowsController extends Controller
{
    /**
     * Display a listing of TV shows.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Get TV shows with optional country filter
        $tvShowsQuery = Tv::with(['genres_data', 'countries', 'seasons.episodes'])
            ->whereNotNull('poster_path')
            ->where('title', '!=', '');

        // Filter by country if specified
        if ($request->has('country')) {
            $countryName = $request->get('country');
            $tvShowsQuery->whereHas('countries', function($query) use ($countryName) {
                $query->where('english_name', $countryName);
            });
        }

        $tvShows = $tvShowsQuery->orderBy('created_at', 'desc')->get();

        // Get sidebar data (same as homepage)
        $mostWatchedMovies = Movie::mostWatched()->take(10)->get();
        $ongoingTvSeries = Tv::ongoing()->take(10)->get();

        return view('tv-shows', compact('tvShows', 'mostWatchedMovies', 'ongoingTvSeries'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Tv;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function show($year)
    {
        // Validate year parameter
        if (!is_numeric($year) || $year < 1900 || $year > 2030) {
            abort(404);
        }

        // Get movies and TV shows for the specified year
        $movies = Movie::whereYear('release_date', $year)
            ->with(['genres', 'countries'])
            ->orderBy('views', 'desc')
            ->get();

        $tvShows = Tv::whereYear('first_air_date', $year)
            ->with(['genres_data', 'countries', 'seasons.episodes'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get sidebar data (same as homepage)
        $mostWatchedMovies = Movie::mostWatched()->take(10)->get();
        $ongoingTvSeries = Tv::ongoing()->take(10)->get();

        return view('year', compact('year', 'movies', 'tvShows', 'mostWatchedMovies', 'ongoingTvSeries'));
    }
}

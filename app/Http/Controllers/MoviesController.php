<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Tv;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    /**
     * Display a listing of movies.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // Get movies with optional country filter
        $moviesQuery = Movie::with(['genres', 'countries'])
            ->whereNotNull('poster_path')
            ->where('title', '!=', '');

        // Filter by country if specified
        if ($request->has('country')) {
            $countryName = $request->get('country');
            $moviesQuery->whereHas('countries', function($query) use ($countryName) {
                $query->where('english_name', $countryName);
            });
        }

        $movies = $moviesQuery->orderBy('created_at', 'desc')->get();

        // Get sidebar data (same as homepage)
        $mostWatchedMovies = Movie::mostWatched()->take(10)->get();
        $ongoingTvSeries = Tv::ongoing()->take(10)->get();

        return view('movies', compact('movies', 'mostWatchedMovies', 'ongoingTvSeries'));
    }
}

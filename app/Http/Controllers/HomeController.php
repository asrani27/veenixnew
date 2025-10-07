<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Movie;
use App\Models\Tv;
use App\Models\Genre;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // The auth middleware is removed from the constructor
        // to allow guests to view the homepage.
        // $this->middleware('auth');
    }

    /**
     * Show the application homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Existing data
        $featuredMovies = Movie::featured()->get();
        $tvSeries = Tv::forHomepage()->get();
        $mostWatchedMovies = Movie::mostWatched()->get();
        $ongoingTvSeries = Tv::ongoing()->get();

        // New sections data
        $dramaKorea = Tv::byCountry('South Korea')->hasEpisodes()->forHomepage()->get();
        $filmIndonesia = Movie::byCountry('Indonesia')->forHomepage()->get();
        $movies = Movie::forHomepage()->get();
        $tvShows = Tv::forHomepage()->get();

        return view('home', compact(
            'featuredMovies', 
            'tvSeries', 
            'mostWatchedMovies', 
            'ongoingTvSeries',
            'dramaKorea',
            'filmIndonesia',
            'movies',
            'tvShows'
        ));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $this->middleware('auth');

        // Get visitor statistics for the last 7 days
        $dailyStats = Visitor::getDailyStats(7);
        $todayStats = Visitor::getTodayStats();
        
        // Get total visitors for the last 30 days
        $monthlyStats = Visitor::getDateRangeStats(
            now()->subDays(30)->toDateString(),
            now()->toDateString()
        );
        
        $totalMonthlyVisits = $monthlyStats->sum('total_visits');
        $totalMonthlyUnique = $monthlyStats->sum('unique_visitors');

        return view('admin.dashboard', compact(
            'dailyStats',
            'todayStats',
            'totalMonthlyVisits',
            'totalMonthlyUnique'
        ));
    }

    /**
     * Show genre page with movies and TV series
     *
     * @param string $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function genre($slug)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();
        
        // Get movies and TV series for this genre
        $movies = $genre->movies()->forHomepage()->get();
        $tvShows = $genre->tvs()->forHomepage()->get();

        // Get sidebar data (same as homepage)
        $mostWatchedMovies = Movie::mostWatched()->take(10)->get();
        $ongoingTvSeries = Tv::ongoing()->take(10)->get();

        return view('genre', compact(
            'genre',
            'movies',
            'tvShows',
            'mostWatchedMovies',
            'ongoingTvSeries'
        ));
    }
}

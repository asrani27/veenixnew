<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\TvController;
use App\Http\Controllers\ApiSettingsController;
use App\Http\Controllers\ResumableUploadController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\TvShowsController;
use App\Http\Controllers\ReportController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/genre/{slug}', [HomeController::class, 'genre'])->name('genre.show');
Route::get('/year/{year}', [YearController::class, 'show'])->name('year.show');
Route::get('/movies', [MoviesController::class, 'index'])->name('movies.index');
Route::get('/tv', [TvShowsController::class, 'index'])->name('tv.index');

// Report routes (public)
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

// Public Resumable Upload Routes (no auth required)
Route::prefix('upload/resumable')->name('upload.resumable.')->group(function () {
    Route::get('/test', [ResumableUploadController::class, 'test'])->name('test');
    Route::post('/', [ResumableUploadController::class, 'upload'])->name('upload');
    Route::get('/', [ResumableUploadController::class, 'checkChunk'])->name('check');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('admin/dashboard', [HomeController::class, 'dashboard']);
    Route::get('admin/tv', [TvController::class, 'index']);

    // Admin Movie Routes
    Route::get('admin/movie', [MovieController::class, 'index'])->name('admin.movies.index');
    Route::get('admin/movie/create', [MovieController::class, 'create'])->name('admin.movies.create');
    Route::post('admin/movie', [MovieController::class, 'store'])->name('admin.movies.store');
    Route::get('admin/movie/{movie}/edit', [MovieController::class, 'edit'])->name('admin.movies.edit');
    Route::put('admin/movie/{movie}', [MovieController::class, 'update'])->name('admin.movies.update');
    Route::delete('admin/movie/{movie}', [MovieController::class, 'destroy'])->name('admin.movies.destroy');
    Route::post('admin/movie/{movie}/upload-video', [MovieController::class, 'uploadVideo'])->name('admin.movies.upload-video');
    Route::get('admin/movies/{movie}/hls-status', [MovieController::class, 'getHlsStatus'])->name('admin.movies.hls-status');
    Route::post('admin/movies/{movie}/retry-hls-conversion', [MovieController::class, 'retryHlsConversion'])->name('admin.movies.retry-hls-conversion');

    // Resumable.js Upload Routes
    Route::get('admin/resumable-upload/test', [ResumableUploadController::class, 'test'])->name('admin.resumable-upload.test');
    Route::post('admin/resumable-upload', [ResumableUploadController::class, 'upload'])->name('admin.resumable-upload.upload');
    Route::get('admin/resumable-upload', [ResumableUploadController::class, 'checkChunk'])->name('admin.resumable-upload.check');
});


Route::middleware(['auth'])->group(function () {

    // TMDB Integration Routes
    Route::get('admin/movie/search-tmdb', [MovieController::class, 'searchTmdb'])->name('admin.movies.search-tmdb');
    Route::post('admin/movie/search-tmdb-api', [MovieController::class, 'searchTmdbApi'])->name('admin.movies.search-tmdb-api');
    Route::get('admin/movie/popular-tmdb', [MovieController::class, 'popularTmdb'])->name('admin.movies.popular-tmdb');
    Route::post('admin/movie/import-from-tmdb/{tmdbId}', [MovieController::class, 'importFromTmdb'])->name('admin.movies.import-from-tmdb');
    Route::get('admin/movie/tmdb-details/{tmdbId}', [MovieController::class, 'getTmdbDetails'])->name('admin.movies.tmdb-details');
    Route::post('admin/movie/bulk-import-tmdb', [MovieController::class, 'bulkImportTmdb'])->name('admin.movies.bulk-import-tmdb');
    // Admin Tv Routes
    Route::get('admin/tv', [TvController::class, 'index'])->name('admin.tv.index');
    Route::get('admin/tv/create', [TvController::class, 'create'])->name('admin.tv.create');
    Route::get('admin/tv/{tv}/edit', [TvController::class, 'edit'])->name('admin.tv.edit');
    Route::put('admin/tv/{tv}', [TvController::class, 'update'])->name('admin.tv.update');
    Route::delete('admin/tv/{tv}', [TvController::class, 'destroy'])->name('admin.tv.destroy');

    // TV Series TMDB Integration Routes
    Route::get('admin/tv/search-tmdb', [TvController::class, 'searchTmdb'])->name('admin.tv.search-tmdb');
    Route::post('admin/tv/search-tmdb-api', [TvController::class, 'searchTmdbApi'])->name('admin.tv.search-tmdb-api');
    Route::get('admin/tv/popular-tmdb', [TvController::class, 'popularTmdb'])->name('admin.tv.popular-tmdb');
    Route::post('admin/tv/import-from-tmdb/{tmdbId}', [TvController::class, 'importFromTmdb'])->name('admin.tv.import-from-tmdb');
    Route::get('admin/tv/tmdb-details/{tmdbId}', [TvController::class, 'getTmdbDetails'])->name('admin.tv.tmdb-details');
    Route::post('admin/tv/bulk-import-tmdb', [TvController::class, 'bulkImportTmdb'])->name('admin.tv.bulk-import-tmdb');

    // TV Series Seasons and Episodes Routes
    Route::get('admin/tv/{tv}/seasons', [TvController::class, 'getSeasons'])->name('admin.tv.seasons');
    Route::get('admin/tv/{tvId}/seasons-page', [TvController::class, 'seasonsPage'])->name('admin.tv.seasons-page');
    Route::get('admin/tv/{tv}/season/{season}', [TvController::class, 'getSeasonDetails'])->name('admin.tv.season.details');
    Route::get('admin/tv/{tv}/season/{season}/episodes', [TvController::class, 'getEpisodes'])->name('admin.tv.season.episodes');
    Route::get('admin/tv/{tv}/season/{season}/edit', [TvController::class, 'editSeason'])->name('admin.tv.season.edit');
    Route::put('admin/tv/{tv}/season/{season}', [TvController::class, 'updateSeason'])->name('admin.tv.season.update');
    Route::get('admin/tv/{tv}/season/{season}/episode/create', [TvController::class, 'createEpisode'])->name('admin.tv.episode.create');
    Route::post('admin/tv/{tv}/season/{season}/episode/import', [TvController::class, 'importEpisode'])->name('admin.tv.episode.import');
    Route::get('admin/tv/{tv}/season/{season}/episode/{episode}', [TvController::class, 'getEpisodeDetails'])->name('admin.tv.episode.details');
    Route::get('admin/tv/{tv}/season/{season}/episode/{episode}/edit', [TvController::class, 'editEpisode'])->name('admin.tv.episode.edit');
    Route::put('admin/tv/{tv}/season/{season}/episode/{episode}', [TvController::class, 'updateEpisode'])->name('admin.tv.episode.update');
    Route::patch('admin/tv/{tv}/season/{season}/episode/{episode}/toggle-publish', [TvController::class, 'togglePublishEpisode'])->name('admin.tv.episode.toggle-publish');
    Route::delete('admin/tv/{tv}/season/{season}/episode/{episode}', [TvController::class, 'destroyEpisode'])->name('admin.tv.episode.destroy');
    Route::get('admin/tv/{tv}/season/{season}/episodes-page', [TvController::class, 'episodesPage'])->name('admin.tv.episodes-page');
    Route::post('admin/tv/{tv}/season/{season}/episode', [TvController::class, 'storeEpisode'])->name('admin.tv.episode.store');
    Route::post('admin/tv/{tv}/import-seasons', [TvController::class, 'importSeasonsAndEpisodes'])->name('admin.tv.import-seasons');

    // Episode HLS Routes
    Route::get('admin/episodes/{episode}/hls-status', [EpisodeController::class, 'getHlsStatus'])->name('admin.episodes.hls-status');
    Route::post('admin/episodes/{episode}/retry-hls-conversion', [EpisodeController::class, 'retryHlsConversion'])->name('admin.episodes.retry-hls-conversion');
    Route::post('admin/episodes/{episode}/upload-video', [EpisodeController::class, 'uploadVideo'])->name('admin.episodes.upload-video');
    Route::get('admin/episodes/{episode}/check-chunk', [EpisodeController::class, 'checkChunk'])->name('admin.episodes.check-chunk');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // API Settings Routes
    Route::get('admin/api-settings', [ApiSettingsController::class, 'index'])->name('admin.api-settings');
    Route::post('admin/api-settings', [ApiSettingsController::class, 'update'])->name('admin.api-settings.update');

    // Reports Management Routes
    Route::get('admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('admin/reports/{report}', [ReportController::class, 'show'])->name('admin.reports.show');
    Route::put('admin/reports/{report}', [ReportController::class, 'update'])->name('admin.reports.update');
    Route::delete('admin/reports/{report}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');
});

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LogoutController::class, 'logout'])->name('logout');
Route::get('/movie/{slug}', [MovieController::class, 'show'])->name('movie.show');
Route::get('/movie/stream/{tmdb_id}', [StreamController::class, 'movieStream'])->name('movie.stream');
Route::get('/movie/download/{slug}', [MovieController::class, 'download'])->name('movie.download');
Route::get('/tv/{slug}', [TvController::class, 'show'])->name('tv.show');
Route::get('/tv/stream/{slug}/season/{season_number}/episode/{episode_number}', [StreamController::class, 'episodeStream'])->name('tv.episode.stream');
Route::get('/tv/stream/{slug}', [StreamController::class, 'tvStreamBySlug'])->name('tv.stream.slug');
Route::get('/tv/stream/{tmdb_id}', [StreamController::class, 'tvStream'])->name('tv.stream');
Route::get('/tv/{slug}/season/{season_number}/episode/{episode_number}', [TvController::class, 'showEpisode'])->name('tv.episode.show');
Route::get('/tv/download/{slug}', [TvController::class, 'download'])->name('tv.download');

// HLS Proxy Routes
Route::get('/hls-proxy/{path}', [StreamController::class, 'hlsProxy'])->where('path', '.*');

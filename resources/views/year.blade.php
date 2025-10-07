@extends('layouts.visitor')

@section('title', $year . ' - veenix')
@section('description', 'Nonton film dan TV series tahun ' . $year . ' subtitle Indonesia. Streaming movie & TV series
dengan kualitas HD gratis di veenix.')
@section('keywords', 'nonton film, streaming movie, TV series, tahun ' . $year . ', subtitle Indonesia, gratis, HD')
@section('canonical', url('/year/' . $year))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content (Left Column - 3/4 width on large screens) -->
        <main class="lg:col-span-3">
            <!-- Year Header -->
            <div class="py-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">Tahun: {{ $year }}</h1>
                        <p class="text-gray-400">Temukan film dan TV series tahun {{ $year }} terbaru</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-400">Total Content</p>
                            <p class="text-2xl font-bold text-white">{{ $movies->count() + $tvShows->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combined Content Section -->
            @php
            $allContent = $movies->concat($tvShows)->sortByDesc(function($item) {
            if ($item instanceof \App\Models\Movie) {
            return $item->views ?? 0;
            } elseif ($item instanceof \App\Models\Tv) {
            // Get total views from all episodes
            return $item->seasons->sum(function($season) {
            return $season->episodes->sum('views');
            });
            }
            return 0;
            })->take(12);
            @endphp

            @if($allContent->count() > 0)
            <section class="py-4" aria-labelledby="content-heading">
                <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-2 md:gap-3">
                    @foreach($allContent as $item)
                    @php
                    $isMovie = $item instanceof \App\Models\Movie;
                    $isTv = $item instanceof \App\Models\Tv;
                    @endphp

                    @if($isMovie)
                    <a href="/movie/{{ $item->slug }}" class="group cursor-pointer flex-shrink-0 w-32 sm:w-auto">
                        <div
                            class="relative overflow-hidden rounded-xl bg-gray-800 shadow-md group-hover:shadow-xl transition-all duration-300 ease-in-out transform group-hover:-translate-y-1">
                            <img src="{{ $item->poster_url }}" alt="{{ $item->title }}"
                                class="w-full h-auto aspect-[2/3] object-cover transition-transform duration-300 group-hover:scale-110"
                                loading="lazy" decoding="async">
                            <!-- Overlay with badges -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                            <!-- Type Badge - Top Left -->
                            <div
                                class="absolute top-1 left-1 bg-violet-600/90 backdrop-blur-sm text-white px-1.5 py-0.5 rounded text-[10px] font-bold z-10 sm:top-2 sm:left-2 sm:px-2 sm:py-1 sm:text-xs">
                                <i class="fas fa-film mr-1"></i>Movie
                            </div>
                            <!-- IMDB Rating -->
                            @if($item->vote_average)
                            <div
                                class="absolute top-1 right-1 bg-black/70 backdrop-blur-sm text-yellow-400 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center z-10 sm:top-2 sm:right-2 sm:px-2 sm:py-1 sm:text-xs">
                                <i class="fas fa-star mr-0.5 sm:mr-1 text-[8px] sm:text-xs"></i>
                                {{ number_format($item->vote_average, 1) }}
                            </div>
                            @endif
                            <!-- Quality Badge - Bottom Right -->
                            @if($item->quality)
                            <div
                                class="absolute bottom-1 right-1 bg-violet-600/90 backdrop-blur-sm text-white px-1.5 py-0.5 rounded text-[10px] font-bold z-10 sm:bottom-2 sm:right-2 sm:px-2 sm:py-1 sm:text-xs">
                                {{ $item->quality }}
                            </div>
                            @endif
                        </div>
                        <div class="mt-3 text-center">
                            <h3
                                class="font-semibold text-sm text-white truncate group-hover:text-violet-400 transition-colors">
                                {{ $item->title }}
                            </h3>
                            <div class="flex items-center justify-center text-gray-400 text-xs mt-1">
                                <span class="flex items-center"><i class="fas fa-eye mr-1"></i>
                                    @if($isMovie)
                                    {{ $item->views ?? 0 }}
                                    @else
                                    {{ $item->seasons->sum(function($season) {
                                    return $season->episodes->sum('views');
                                    }) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                    @elseif($isTv)
                    <div class="group cursor-pointer flex-shrink-0 w-32 sm:w-auto">
                        <a href="/tv/{{ $item->slug }}" class="block">
                            <div
                                class="relative overflow-hidden rounded-xl bg-gray-800 shadow-md group-hover:shadow-xl transition-all duration-300 ease-in-out transform group-hover:-translate-y-1">
                                <img src="{{ $item->poster_url }}" alt="{{ $item->title }}"
                                    class="w-full h-auto aspect-[2/3] object-cover transition-transform duration-300 group-hover:scale-110"
                                    loading="lazy" decoding="async">
                                <!-- Overlay with badges -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <!-- Type Badge - Top Left -->
                                <div
                                    class="absolute top-1 left-1 bg-green-600/90 backdrop-blur-sm text-white px-1.5 py-0.5 rounded text-[10px] font-bold z-10 sm:top-2 sm:left-2 sm:px-2 sm:py-1 sm:text-xs">
                                    <i class="fas fa-tv mr-1"></i>TV
                                </div>
                                <!-- IMDB Rating -->
                                @if($item->vote_average)
                                <div
                                    class="absolute top-1 right-1 bg-black/70 backdrop-blur-sm text-yellow-400 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center z-10 sm:top-2 sm:right-2 sm:px-2 sm:py-1 sm:text-xs">
                                    <i class="fas fa-star mr-0.5 sm:mr-1 text-[8px] sm:text-xs"></i>
                                    {{ number_format($item->vote_average, 1) }}
                                </div>
                                @endif
                                <!-- Prominent Latest Episode Badge (Center) -->
                                @if($item->seasons && $item->seasons->isNotEmpty())
                                @php
                                $latestSeason = $item->seasons->sortByDesc('season_number')->first();
                                $latestEpisode = $latestSeason ?
                                $latestSeason->episodes->where('publish', true)->sortByDesc('episode_number')->first() :
                                null;
                                @endphp
                                @if($latestEpisode)
                                <a href="/tv/{{ $item->slug }}/season/{{ $latestSeason->season_number }}/episode/{{ $latestEpisode->episode_number }}"
                                    class="absolute inset-0 flex items-center justify-center z-20 hover:opacity-80 transition-opacity"
                                    title="Watch Episode {{ $latestEpisode->episode_number }}"
                                    onclick="event.stopPropagation()">
                                    <div
                                        class="bg-black/50 backdrop-blur-sm rounded-full w-12 h-12 flex items-center justify-center shadow-lg border-2 border-dashed border-white sm:w-16 sm:h-16 hover:bg-green-600/50 transition-colors cursor-pointer">
                                        <span class="text-white text-sm font-bold drop-shadow-lg sm:text-lg">E{{
                                            $latestEpisode->episode_number }}</span>
                                    </div>
                                </a>
                                @endif
                                @endif
                            </div>
                        </a>
                        <a href="/tv/{{ $item->slug }}"
                            class="block mt-3 text-center group-hover:text-violet-400 transition-colors">
                            <h3 class="font-semibold text-sm text-white truncate">
                                {{ $item->title }}
                            </h3>
                            <div class="flex items-center justify-center text-gray-400 text-xs mt-1">
                                <span class="flex items-center"><i class="fas fa-eye mr-1"></i>
                                    {{ $item->seasons->sum(function($season) {
                                    return $season->episodes->sum('views');
                                    }) }}
                                </span>
                            </div>
                        </a>
                    </div>
                    @endif
                    @endforeach
                </div>
            </section>
            @endif

            <!-- No Content Message -->
            @if($movies->count() == 0 && $tvShows->count() == 0)
            <div class="text-center py-16">
                <i class="fas fa-calendar text-6xl text-gray-600 mb-4"></i>
                <h2 class="text-2xl font-bold text-white mb-2">No Content Found</h2>
                <p class="text-gray-400">No movies or TV series found for year {{ $year }}</p>
                <a href="/"
                    class="inline-flex items-center px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors mt-4">
                    <i class="fas fa-home mr-2"></i>
                    Back to Home
                </a>
            </div>
            @endif
        </main>

        <!-- Right Sidebar (1/4 width on large screens) -->
        <aside class="lg:col-span-1 mt-4" aria-label="Sidebar">
            <!-- Join Telegram Banner -->
            <section
                class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 mb-6 shadow-lg transform hover:scale-105 transition-transform duration-300">
                <div class="text-center">
                    <div class="flex justify-center mb-3">
                        <i class="fab fa-telegram text-4xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Join Telegram Channel</h3>
                    <p class="text-blue-100 text-sm mb-4">Dapatkan update terbaru film & drama Korea</p>
                    <a href="https://t.me/+ufXYbLGapjo4YWQ1" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors duration-200 shadow-md">
                        <i class="fab fa-telegram mr-2"></i>
                        Join Now
                    </a>
                </div>
            </section>
            <!-- Paling Banyak Di Tonton Section -->
            <section class="bg-gray-800 rounded-lg p-4 mb-6" aria-labelledby="most-watched-heading">
                <h2 id="most-watched-heading" class="text-xl font-bold text-white mb-4 flex items-center">
                    <i class="fas fa-fire text-red-500 mr-2"></i>
                    Paling Banyak Di Tonton
                </h2>
                <div class="space-y-0">
                    @foreach($mostWatchedMovies as $movie)
                    <a href="/movie/{{ $movie->slug }}"
                        class="group flex items-center space-x-2 py-1 rounded-lg hover:bg-gray-700 transition-colors">
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                            class="w-12 h-16 object-cover rounded" loading="lazy" decoding="async">
                        <div class="flex-1 min-w-0">
                            <h4
                                class="text-xs font-medium text-white truncate group-hover:text-violet-400 transition-colors mb-1">
                                {{ $movie->title }}</h4>
                            @if($movie->vote_average)
                            <div class="flex items-center text-xs text-gray-400 mb-0.5">
                                <i class="fas fa-star mr-1 text-xs text-yellow-400"></i>
                                {{ number_format($movie->vote_average, 1) }}
                            </div>
                            @endif
                            <div class="flex items-center text-xs text-gray-400">
                                <i class="fas fa-eye mr-1 text-xs"></i>
                                {{ $movie->views ?? 0 }}
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>

            <!-- Drama Yang Sedang Ongoing Section -->
            <section class="bg-gray-800 rounded-lg p-4" aria-labelledby="ongoing-drama-heading">
                <h2 id="ongoing-drama-heading" class="text-xl font-bold text-white mb-4 flex items-center">
                    <i class="fas fa-play-circle text-green-500 mr-2"></i>
                    Drama Ongoing
                </h2>
                <div class="space-y-0">
                    @foreach($ongoingTvSeries as $show)
                    <a href="/tv/{{ $show->slug }}"
                        class="group flex items-center space-x-2 py-1 rounded-lg hover:bg-gray-700 transition-colors">
                        <img src="{{ $show->poster_url }}" alt="{{ $show->title }}"
                            class="w-12 h-16 object-cover rounded" loading="lazy" decoding="async">
                        <div class="flex-1 min-w-0">
                            <h4
                                class="text-xs font-medium text-white truncate group-hover:text-violet-400 transition-colors">
                                {{ $show->title }}</h4>
                            <div class="flex items-center text-xs text-gray-400 mt-0.5">
                                <span class="flex items-center">
                                    <i class="fas fa-eye mr-1 text-xs"></i>
                                    {{ $show->views ?? 0 }}
                                </span>
                                @if($show->vote_average)
                                <span class="ml-2 flex items-center">
                                    <i class="fas fa-star mr-1 text-xs text-yellow-400"></i>
                                    {{ number_format($show->vote_average, 1) }}
                                </span>
                                @endif
                            </div>
                            @if($show->status)
                            <div class="flex items-center mt-0.5">
                                <span
                                    class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-green-600/20 text-green-400">
                                    {{ $show->status }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
</div>

@endsection
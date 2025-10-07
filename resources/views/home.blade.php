@extends('layouts.visitor')

@section('title', 'veenix - Nonton Film dan Drama Korea Terbaru')
@section('description', 'Nonton film dan drama Korea terbaru subtitle Indonesia. Streaming movie & TV series dengan
kualitas HD gratis di veenix.')
@section('keywords', 'nonton film, streaming movie, drama Korea, film Indonesia, TV series, subtitle Indonesia, gratis,
HD')
@section('canonical', url('/'))

@section('content')
<!-- Structured Data for Homepage -->
{{-- <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "veenix",
    "alternateName": "veenix Streaming",
    "url": "{{ url('/') }}",
    "description": "Nonton film dan drama Korea terbaru subtitle Indonesia. Streaming movie & TV series dengan kualitas HD gratis di veenix.",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/search') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    },
    "publisher": {
        "@type": "Organization",
        "name": "veenix",
        "url": "{{ url('/') }}"
    }
}
</script> --}}

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content (Left Column - 3/4 width on large screens) -->
        <main class="lg:col-span-3">
            <!-- Drama Korea Section -->
            <section class="py-4" aria-labelledby="drama-korea-heading">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="drama-korea-heading" class="text-2xl sm:text-3xl font-bold text-white">Drama Korea</h2>
                    <a href="/tv?country=South+Korea"
                        class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors duration-200">
                        View More
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div
                    class="flex sm:grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 sm:gap-2 md:gap-3 overflow-x-auto sm:overflow-visible pb-4 sm:pb-0 scroll-smooth scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0">
                    @foreach($dramaKorea->take(8) as $show)
                    <div class="group cursor-pointer flex-shrink-0 w-32 sm:w-auto">
                        <a href="/tv/{{ $show->slug }}" class="block">
                            <div
                                class="relative overflow-hidden rounded-xl bg-gray-800 shadow-md group-hover:shadow-xl transition-all duration-300 ease-in-out transform group-hover:-translate-y-1">
                                <img src="{{ $show->poster_url }}" alt="{{ $show->title }}"
                                    class="w-full h-auto aspect-[2/3] object-cover transition-transform duration-300 group-hover:scale-110"
                                    loading="lazy" decoding="async">
                                <!-- Overlay with badges -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <!-- IMDB Rating - Top Left -->
                                @if($show->vote_average)
                                <div
                                    class="absolute top-1 left-1 bg-black/70 backdrop-blur-sm text-yellow-400 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center z-10 sm:top-2 sm:left-2 sm:px-2 sm:py-1 sm:text-xs">
                                    <i class="fas fa-star mr-0.5 sm:mr-1 text-[8px] sm:text-xs"></i>
                                    {{ number_format($show->vote_average, 1) }}
                                </div>
                                @endif
                                <!-- Prominent Latest Episode Badge (Center) -->
                                @if($show->seasons && $show->seasons->isNotEmpty())
                                @php
                                $latestSeason = $show->seasons->sortByDesc('season_number')->first();
                                $latestEpisode = $latestSeason ?
                                $latestSeason->episodes->where('publish', true)->sortByDesc('episode_number')->first() : null;
                                @endphp
                                @if($latestEpisode)
                                <a href="/tv/{{ $show->slug }}/season/{{ $latestSeason->season_number }}/episode/{{ $latestEpisode->episode_number }}"
                                    class="absolute inset-0 flex items-center justify-center z-20 hover:opacity-80 transition-opacity"
                                    title="Watch Episode {{ $latestEpisode->episode_number }}"
                                    onclick="event.stopPropagation()">
                                    <div
                                        class="bg-black/50 backdrop-blur-sm rounded-full w-12 h-12 flex items-center justify-center shadow-lg border-2 border-dashed border-white sm:w-16 sm:h-16 hover:bg-violet-600/50 transition-colors cursor-pointer">
                                        <span class="text-white text-sm font-bold drop-shadow-lg sm:text-lg">E{{
                                            $latestEpisode->episode_number }}</span>
                                    </div>
                                </a>
                                @endif
                                @endif
                            </div>
                        </a>
                        <a href="/tv/{{ $show->slug }}"
                            class="block mt-3 text-center group-hover:text-violet-400 transition-colors">
                            <h3 class="font-semibold text-sm text-white truncate">
                                {{ $show->title }}
                            </h3>
                            <div class="flex items-center justify-center text-gray-400 text-xs mt-1">
                                <span class="flex items-center"><i class="fas fa-eye mr-1"></i>{{ $show->views ?? 0
                                    }}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Divider Line -->
            <div class="border-t border-gray-700 my-6"></div>

            <!-- Film Indonesia Section -->
            <section class="py-4" aria-labelledby="film-indonesia-heading">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="film-indonesia-heading" class="text-2xl sm:text-3xl font-bold text-white">Film Indonesia
                    </h2>
                    <a href="/movies?country=Indonesia"
                        class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors duration-200">
                        View More
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div
                    class="flex sm:grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 sm:gap-2 md:gap-3 overflow-x-auto sm:overflow-visible pb-4 sm:pb-0 scroll-smooth scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0">
                    @foreach($filmIndonesia->take(8) as $movie)
                    <a href="/movie/{{ $movie->slug }}" class="group cursor-pointer flex-shrink-0 w-32 sm:w-auto">
                        <div
                            class="relative overflow-hidden rounded-xl bg-gray-800 shadow-md group-hover:shadow-xl transition-all duration-300 ease-in-out transform group-hover:-translate-y-1">
                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                                class="w-full h-auto aspect-[2/3] object-cover transition-transform duration-300 group-hover:scale-110"
                                loading="lazy" decoding="async">
                            <!-- Overlay with badges -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                            <!-- IMDB Rating - Top Left -->
                            @if($movie->vote_average)
                            <div
                                class="absolute top-1 left-1 bg-black/70 backdrop-blur-sm text-yellow-400 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center z-10 sm:top-2 sm:left-2 sm:px-2 sm:py-1 sm:text-xs">
                                <i class="fas fa-star mr-0.5 sm:mr-1 text-[8px] sm:text-xs"></i>
                                {{ number_format($movie->vote_average, 1) }}
                            </div>
                            @endif
                            <!-- Quality Badge - Top Right -->
                            @if($movie->quality)
                            <div
                                class="absolute top-1 right-1 bg-violet-600/90 backdrop-blur-sm text-white px-1.5 py-0.5 rounded text-[10px] font-bold z-10 sm:top-2 sm:right-2 sm:px-2 sm:py-1 sm:text-xs">
                                {{ $movie->quality }}
                            </div>
                            @endif
                        </div>
                        <div class="mt-3 text-center">
                            <h3
                                class="font-semibold text-sm text-white truncate group-hover:text-violet-400 transition-colors">
                                {{ $movie->title }}
                            </h3>
                            <div class="flex items-center justify-center text-gray-400 text-xs mt-1">
                                <span class="flex items-center"><i class="fas fa-eye mr-1"></i>{{ $movie->views ?? 0
                                    }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>

            <!-- Divider Line -->
            <div class="border-t border-gray-700 my-6"></div>

            <!-- Movies Section -->
            <section class="py-4" aria-labelledby="movies-heading">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="movies-heading" class="text-2xl sm:text-3xl font-bold text-white">Movies</h2>
                    <a href="/movies"
                        class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors duration-200">
                        View More
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div
                    class="flex sm:grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 sm:gap-2 md:gap-3 overflow-x-auto sm:overflow-visible pb-4 sm:pb-0 scroll-smooth scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0">
                    @foreach($movies->take(8) as $movie)
                    <a href="/movie/{{ $movie->slug }}" class="group cursor-pointer flex-shrink-0 w-32 sm:w-auto">
                        <div
                            class="relative overflow-hidden rounded-xl bg-gray-800 shadow-md group-hover:shadow-xl transition-all duration-300 ease-in-out transform group-hover:-translate-y-1">
                            <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
                                class="w-full h-auto aspect-[2/3] object-cover transition-transform duration-300 group-hover:scale-110"
                                loading="lazy" decoding="async">
                            <!-- Overlay with badges -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                            <!-- IMDB Rating - Top Left -->
                            @if($movie->vote_average)
                            <div
                                class="absolute top-1 left-1 bg-black/70 backdrop-blur-sm text-yellow-400 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center z-10 sm:top-2 sm:left-2 sm:px-2 sm:py-1 sm:text-xs">
                                <i class="fas fa-star mr-0.5 sm:mr-1 text-[8px] sm:text-xs"></i>
                                {{ number_format($movie->vote_average, 1) }}
                            </div>
                            @endif
                            <!-- Quality Badge - Top Right -->
                            @if($movie->quality)
                            <div
                                class="absolute top-1 right-1 bg-violet-600/90 backdrop-blur-sm text-white px-1.5 py-0.5 rounded text-[10px] font-bold z-10 sm:top-2 sm:right-2 sm:px-2 sm:py-1 sm:text-xs">
                                {{ $movie->quality }}
                            </div>
                            @endif
                        </div>
                        <div class="mt-3 text-center">
                            <h3
                                class="font-semibold text-sm text-white truncate group-hover:text-violet-400 transition-colors">
                                {{ $movie->title }}
                            </h3>
                            <div class="flex items-center justify-center text-gray-400 text-xs mt-1">
                                <span class="flex items-center"><i class="fas fa-eye mr-1"></i>{{ $movie->views ?? 0
                                    }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>

            <!-- Divider Line -->
            <div class="border-t border-gray-700 my-6"></div>

            <!-- TV Shows Section -->
            <section class="py-4" aria-labelledby="tv-series-heading">
                <div class="flex justify-between items-center mb-4">
                    <h2 id="tv-series-heading" class="text-2xl sm:text-3xl font-bold text-white">TV Series</h2>
                    <a href="/tv"
                        class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 border border-transparent text-xs sm:text-sm font-medium rounded-md text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors duration-200">
                        View More
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div
                    class="flex sm:grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1 sm:gap-2 md:gap-3 overflow-x-auto sm:overflow-visible pb-4 sm:pb-0 scroll-smooth scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0">
                    @foreach($tvShows->take(8) as $show)
                    <div class="group cursor-pointer flex-shrink-0 w-32 sm:w-auto">
                        <a href="/tv/{{ $show->slug }}" class="block">
                            <div
                                class="relative overflow-hidden rounded-xl bg-gray-800 shadow-md group-hover:shadow-xl transition-all duration-300 ease-in-out transform group-hover:-translate-y-1">
                                <img src="{{ $show->poster_url }}" alt="{{ $show->title }}"
                                    class="w-full h-auto aspect-[2/3] object-cover transition-transform duration-300 group-hover:scale-110"
                                    loading="lazy" decoding="async">
                                <!-- Overlay with badges -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                                <!-- IMDB Rating - Top Left -->
                                @if($show->vote_average)
                                <div
                                    class="absolute top-1 left-1 bg-black/70 backdrop-blur-sm text-yellow-400 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center z-10 sm:top-2 sm:left-2 sm:px-2 sm:py-1 sm:text-xs">
                                    <i class="fas fa-star mr-0.5 sm:mr-1 text-[8px] sm:text-xs"></i>
                                    {{ number_format($show->vote_average, 1) }}
                                </div>
                                @endif
                                <!-- Prominent Latest Episode Badge (Center) -->
                                @if($show->seasons && $show->seasons->isNotEmpty())
                                @php
                                $latestSeason = $show->seasons->sortByDesc('season_number')->first();
                                $latestEpisode = $latestSeason ?
                                $latestSeason->episodes->where('publish', true)->sortByDesc('episode_number')->first() : null;
                                @endphp
                                @if($latestEpisode)
                                <a href="/tv/{{ $show->slug }}/season/{{ $latestSeason->season_number }}/episode/{{ $latestEpisode->episode_number }}"
                                    class="absolute inset-0 flex items-center justify-center z-20 hover:opacity-80 transition-opacity"
                                    title="Watch Episode {{ $latestEpisode->episode_number }}"
                                    onclick="event.stopPropagation()">
                                    <div
                                        class="bg-black/50 backdrop-blur-sm rounded-full w-12 h-12 flex items-center justify-center shadow-lg border-2 border-dashed border-white sm:w-16 sm:h-16 hover:bg-violet-600/50 transition-colors cursor-pointer">
                                        <span class="text-white text-sm font-bold drop-shadow-lg sm:text-lg">E{{
                                            $latestEpisode->episode_number }}</span>
                                    </div>
                                </a>
                                @endif
                                @endif
                            </div>
                        </a>
                        <a href="/tv/{{ $show->slug }}"
                            class="block mt-3 text-center group-hover:text-violet-400 transition-colors">
                            <h3 class="font-semibold text-sm text-white truncate">
                                {{ $show->title }}
                            </h3>
                            <div class="flex items-center justify-center text-gray-400 text-xs mt-1">
                                <span class="flex items-center"><i class="fas fa-eye mr-1"></i>{{ $show->views ?? 0
                                    }}</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </section>
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

{{-- @push('scripts')
<script>
    // Truncate long movie titles
        function truncateMovieTitles() {
            const movieTitles = document.querySelectorAll('.mt-2.text-left h4');
            const maxLength = 58; // Adjusted for desired truncation length
            
            movieTitles.forEach(title => {
                const originalText = title.textContent;
                
                if (originalText.length > maxLength) {
                    // Find the last space before the max length to avoid cutting words
                    let truncatedText = originalText.substring(0, maxLength);
                    const lastSpaceIndex = truncatedText.lastIndexOf(' ');
                    
                    if (lastSpaceIndex > 0) {
                        truncatedText = truncatedText.substring(0, lastSpaceIndex);
                    }
                    
                    title.textContent = truncatedText + '...';
                }
            });
        }

        // Call the function when the page loads
        document.addEventListener('DOMContentLoaded', truncateMovieTitles);

        // No slider functionality needed - using simple grid layout
</script>
@endpush --}}

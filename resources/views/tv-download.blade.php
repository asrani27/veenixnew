@extends('layouts.visitor')

@section('title')
Download {{ $tv->title }} - veenix
@endsection

@section('content')
<!-- Header Section -->
<section class="bg-gradient-to-r from-violet-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <!-- TV Series Poster -->
            <div class="flex-shrink-0">
                <img src="{{ $tv->poster_url }}" alt="{{ $tv->title }}" class="w-48 h-72 object-cover rounded-xl shadow-2xl">
            </div>

            <!-- TV Series Info -->
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl font-bold mb-4">Download {{ $tv->title }}</h1>
                
                <!-- TV Series Meta Info -->
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 mb-6">
                    @if($tv->first_air_date)
                    <span class="text-white/90">
                        {{ \Carbon\Carbon::parse($tv->first_air_date)->format('Y') }}
                        @if($tv->last_air_date && $tv->first_air_date->year != $tv->last_air_date->year)
                        - {{ \Carbon\Carbon::parse($tv->last_air_date)->format('Y') }}
                        @endif
                    </span>
                    @endif

                    @if($tv->status)
                    <span class="bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tv->status }}
                    </span>
                    @endif

                    @if($tv->vote_average)
                    <span class="flex items-center text-yellow-300">
                        <i class="fas fa-star mr-1"></i>
                        {{ number_format($tv->vote_average, 1) }}
                    </span>
                    @endif

                    @if($tv->number_of_seasons)
                    <span class="text-white/90">{{ $tv->number_of_seasons }} Season{{ $tv->number_of_seasons > 1 ? 's' : '' }}</span>
                    @endif
                </div>

                <!-- Genres -->
                @if($tv->genres_data && $tv->genres_data->count() > 0)
                <div class="flex flex-wrap gap-2 mb-6 justify-center md:justify-start">
                    @foreach($tv->genres_data as $genre)
                    <span class="bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm">
                        {{ $genre->name }}
                    </span>
                    @endforeach
                </div>
                @endif

                <!-- Back to Watch Button -->
                <div class="flex gap-4 justify-center md:justify-start">
                    <a href="/tv/{{ $tv->slug }}" 
                        class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-play"></i>
                        Watch Online
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Download Instructions -->
<section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-100">
    <div class="max-w-7xl mx-auto">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">How to Download</h3>
                    <ul class="text-blue-800 space-y-1">
                        <li>• Click on any episode to view available download links</li>
                        <li>• Choose your preferred quality (540p or 720p)</li>
                        <li>• Click the download button to start downloading</li>
                        <li>• Some episodes may have multiple download sources</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Episodes Download Section -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-white">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold mb-8 text-gray-900">Available Episodes</h2>

        @if($tv->seasons && $tv->seasons->count() > 0)
        <div class="space-y-8">
            @foreach($tv->seasons as $season)
            @if($season->episodes && $season->episodes->count() > 0)
            <div class="bg-gray-50 rounded-xl overflow-hidden shadow-sm">
                <!-- Season Header -->
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 text-white p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            @if($season->local_poster_path)
                            <img src="{{ asset('storage/' . $season->local_poster_path) }}" alt="{{ $season->name }}"
                                class="w-16 h-24 object-cover rounded-lg">
                            @elseif($season->poster_path)
                            <img src="https://image.tmdb.org/t/p/w185{{ $season->poster_path }}"
                                alt="{{ $season->name }}" class="w-16 h-24 object-cover rounded-lg">
                            @else
                            <div class="w-16 h-24 bg-gray-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tv text-gray-400 text-xl"></i>
                            </div>
                            @endif

                            <div>
                                <h3 class="text-xl font-bold">{{ $season->name }}</h3>
                                @if($season->air_date)
                                <p class="text-gray-300 text-sm">{{ \Carbon\Carbon::parse($season->air_date)->format('F j, Y') }}</p>
                                @endif
                                <p class="text-gray-300 text-sm">{{ $season->episode_count }} Episodes</p>
                            </div>
                        </div>

                        <div class="text-sm text-gray-300">
                            {{ count($season->episodes) }} Available for Download
                        </div>
                    </div>
                </div>

                <!-- Episodes List -->
                <div class="p-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                        @foreach($season->episodes as $episode)
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                            <!-- Episode Still -->
                            @if($episode->local_still_path || $episode->still_path)
                            <div class="aspect-[16/9] bg-gray-100">
                                @if($episode->local_still_path)
                                <img src="{{ asset('storage/' . $episode->local_still_path) }}" 
                                     alt="{{ $episode->name }}" 
                                     class="w-full h-full object-cover">
                                @else
                                <img src="https://image.tmdb.org/t/p/w300{{ $episode->still_path }}" 
                                     alt="{{ $episode->name }}" 
                                     class="w-full h-full object-cover">
                                @endif
                            </div>
                            @else
                            <div class="aspect-[16/9] bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-play text-gray-400 text-2xl"></i>
                            </div>
                            @endif

                            <!-- Episode Info -->
                            <div class="p-3">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 text-sm truncate">
                                            Ep {{ $episode->episode_number }}
                                        </h4>
                                        <p class="text-xs text-gray-600 truncate">
                                            {{ $episode->name }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="bg-green-100 text-green-800 text-xs px-1.5 py-0.5 rounded-full font-medium">
                                            ✓
                                        </span>
                                    </div>
                                </div>

                                <!-- Download Links -->
                                @if($episode->downloadLinks && $episode->downloadLinks->count() > 0)
                                <div class="space-y-1">
                                    @foreach($episode->downloadLinks->where('is_active', true)->sortBy('sort_order')->take(2) as $link)
                                    <a href="{{ $link->url }}" 
                                       target="_blank"
                                       class="flex items-center justify-center w-full bg-violet-600 hover:bg-violet-700 text-white px-2 py-1.5 rounded text-xs font-medium transition-colors duration-200">
                                        <i class="fas fa-download mr-1"></i>
                                        {{ $link->quality }}
                                        @if($link->label)
                                        - {{ $link->label }}
                                        @endif
                                    </a>
                                    @endforeach
                                    @if($episode->downloadLinks->where('is_active', true)->count() > 2)
                                    <button onclick="showMoreLinks({{ $episode->id }})" 
                                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs font-medium transition-colors duration-200">
                                        +{{ $episode->downloadLinks->where('is_active', true)->count() - 2 }} more
                                    </button>
                                    @endif
                                </div>
                                @else
                                <div class="text-center py-2">
                                    <p class="text-gray-400 text-xs">
                                        No links
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @else
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <i class="fas fa-film text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Episodes Available</h3>
            <p class="text-gray-600 max-w-md mx-auto">
                There are currently no episodes available for download for this TV series.
            </p>
        </div>
        @endif
    </div>
</section>

<!-- Overview Section -->
@if($tv->overview)
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold mb-8 text-gray-900">Synopsis</h2>
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <p class="text-gray-700 leading-relaxed text-lg">{{ $tv->overview }}</p>
        </div>
    </div>
</section>
@endif

<!-- Back to Top Button -->
<div class="fixed bottom-8 right-8 z-40">
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
            class="bg-violet-600 hover:bg-violet-700 text-white p-4 rounded-full shadow-lg transition-all duration-200 hover:scale-110">
        <i class="fas fa-arrow-up"></i>
    </button>
</div>
@endsection

@push('scripts')
<script>
    // Store episode data for show more functionality
    const episodeData = @json($tv->seasons->flatMap(function($season) {
        return $season->episodes->map(function($episode) {
            return [
                'id' => $episode->id,
                'links' => $episode->downloadLinks->where('is_active', true)->sortBy('sort_order')->toArray()
            ];
        });
    }));

    // Show more download links for an episode
    function showMoreLinks(episodeId) {
        const episode = episodeData.find(ep => ep.id === episodeId);
        if (!episode || !episode.links || episode.links.length <= 2) return;

        // Create modal with all download links
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl p-6 max-w-md w-full max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">All Download Links</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-2">
                    ${episode.links.map(link => `
                        <a href="${link.url}" target="_blank"
                           class="flex items-center justify-between w-full bg-violet-600 hover:bg-violet-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-200">
                            <span class="flex items-center gap-2">
                                <i class="fas fa-download"></i>
                                ${link.quality}
                                ${link.label ? ` - ${link.label}` : ''}
                            </span>
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    `).join('')}
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    // Add smooth scroll behavior for better UX
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide back to top button based on scroll position
        const backToTopButton = document.querySelector('.fixed.bottom-8.right-8 button');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'invisible');
                backToTopButton.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton.classList.add('opacity-0', 'invisible');
                backToTopButton.classList.remove('opacity-100', 'visible');
            }
        });
        
        // Initially hide the button
        backToTopButton.classList.add('opacity-0', 'invisible', 'transition-opacity', 'duration-300');
    });
</script>
@endpush

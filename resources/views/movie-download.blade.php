@extends('layouts.visitor')

@section('title')
{{ $movie->title }} - Download - veenix
@endsection

@section('content')
<!-- Section 1: Movie Header -->
<section class="bg-gradient-to-r from-violet-900 via-purple-900 to-indigo-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <!-- Movie Poster -->
            <div class="flex-shrink-0">
                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" 
                    class="w-48 h-72 object-cover rounded-xl shadow-2xl border-4 border-white/20">
            </div>

            <!-- Movie Info -->
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $movie->title }}</h1>
                
                <!-- Movie Meta Info -->
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-6">
                    @if($movie->release_date)
                    <span class="text-white/90 text-lg">{{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}</span>
                    @endif

                    @if($movie->quality)
                    <span class="bg-emerald-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                        {{ $movie->quality }}
                    </span>
                    @endif

                    @if($movie->vote_average)
                    <span class="flex items-center text-yellow-400 text-lg">
                        <i class="fas fa-star mr-1"></i>
                        {{ number_format($movie->vote_average, 1) }}
                    </span>
                    @endif

                    @if($movie->runtime)
                    <span class="text-white/90 text-lg">{{ $movie->runtime }} min</span>
                    @endif
                </div>

                <!-- Genres -->
                @if($movie->genres && $movie->genres->count() > 0)
                <div class="flex flex-wrap gap-2 justify-center md:justify-start mb-6">
                    @foreach($movie->genres as $genre)
                    <span class="bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm">
                        {{ $genre->name }}
                    </span>
                    @endforeach
                </div>
                @endif

                <!-- Overview -->
                @if($movie->overview)
                <p class="text-white/80 text-lg leading-relaxed max-w-3xl">{{ Str::limit($movie->overview, 200) }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Section 2: Download Links -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-800">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-white mb-4">Download {{ $movie->title }}</h2>
            <p class="text-gray-400 text-lg">Choose your preferred quality and download link</p>
        </div>

        @if($movie->downloadLinks && $movie->downloadLinks->count() > 0)
        <div class="space-y-6">
            <!-- Group by quality -->
            @php
                $groupedLinks = $movie->downloadLinks->groupBy('quality');
                $qualityOrder = ['720p' => 0, '540p' => 1];
                $sortedQualities = $groupedLinks->sortBy(function($links, $quality) use ($qualityOrder) {
                    return $qualityOrder[$quality] ?? 999;
                });
            @endphp

            @foreach($sortedQualities as $quality => $links)
            <div class="bg-gray-700 rounded-xl overflow-hidden">
                <!-- Quality Header -->
                <div class="bg-gradient-to-r {{ $quality === '720p' ? 'from-emerald-600 to-teal-600' : 'from-blue-600 to-indigo-600' }} px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-download mr-3"></i>
                        {{ $quality }} Quality
                        <span class="ml-3 text-sm font-normal opacity-90">({{ $links->count() }} link{{ $links->count() > 1 ? 's' : '' }} available)</span>
                    </h3>
                </div>

                <!-- Download Links -->
                <div class="p-6">
                    <div class="grid gap-3">
                        @foreach($links->sortBy('sort_order') as $index => $link)
                            @php
                                $showLink = $index < 3; // Show first 3 links by default
                            @endphp

                            <div class="download-link-item {{ $showLink ? '' : 'hidden-link' }}" 
                                 data-quality="{{ $quality }}" data-index="{{ $index }}">
                                <a href="{{ $link->url }}" 
                                   target="_blank"
                                   class="flex items-center justify-between w-full bg-gray-600 hover:bg-gray-500 text-white px-4 py-3 rounded-lg transition-all duration-200 group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                            <i class="fas fa-external-link-alt text-sm"></i>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-medium">
                                                Download Link {{ $index + 1 }}
                                                @if($link->label)
                                                    <span class="text-emerald-400 ml-2">{{ $link->label }}</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-300">Click to download {{ $quality }} version</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 bg-emerald-600 text-xs rounded-full">{{ $quality }}</span>
                                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Show More/Less Button -->
                    @if($links->count() > 3)
                    <div class="mt-4 text-center">
                        <button onclick="toggleMoreLinks('{{ $quality }}', {{ $links->count() }})" 
                                class="show-more-btn text-emerald-400 hover:text-emerald-300 font-medium transition-colors"
                                data-quality="{{ $quality }}">
                            <i class="fas fa-chevron-down mr-2"></i>
                            Show {{ $links->count() - 3 }} more link{{ $links->count() - 3 > 1 ? 's' : '' }}
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="bg-gray-700 rounded-xl p-8 max-w-2xl mx-auto">
                <i class="fas fa-download text-6xl text-gray-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-white mb-2">No Download Links Available</h3>
                <p class="text-gray-400 mb-6">Download links for this movie will be available soon. Please check back later.</p>
                <a href="/movie/{{ $movie->slug }}" 
                   class="inline-flex items-center bg-violet-600 hover:bg-violet-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Movie
                </a>
            </div>
        </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleMoreLinks(quality, totalLinks) {
    const button = document.querySelector(`[data-quality="${quality}"].show-more-btn`);
    const hiddenLinks = document.querySelectorAll(`.download-link-item[data-quality="${quality}"].hidden-link`);
    const visibleLinks = document.querySelectorAll(`.download-link-item[data-quality="${quality}"]:not(.hidden-link)`);
    
    if (button.classList.contains('expanded')) {
        // Hide extra links
        hiddenLinks.forEach(link => {
            link.style.display = 'none';
        });
        
        // Update button
        button.innerHTML = `<i class="fas fa-chevron-down mr-2"></i>Show ${totalLinks - 3} more link${totalLinks - 3 > 1 ? 's' : ''}`;
        button.classList.remove('expanded');
    } else {
        // Show hidden links with animation
        hiddenLinks.forEach((link, index) => {
            setTimeout(() => {
                link.style.display = 'block';
                link.style.opacity = '0';
                link.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    link.style.transition = 'all 0.3s ease';
                    link.style.opacity = '1';
                    link.style.transform = 'translateY(0)';
                }, 50);
            }, index * 100);
        });
        
        // Update button
        button.innerHTML = `<i class="fas fa-chevron-up mr-2"></i>Show less`;
        button.classList.add('expanded');
    }
}

// Add hover effects to download links
document.addEventListener('DOMContentLoaded', function() {
    const downloadLinks = document.querySelectorAll('.download-link-item a');
    
    downloadLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});
</script>
@endpush

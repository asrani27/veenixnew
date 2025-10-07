@extends('layouts.visitor')

@section('title')
{{ $tv->title }} - veenix
@endsection

@section('content')
<!-- Section 1: Video Player -->
<section class="bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(isset($season_number) && isset($episode_number))
        <!-- Episode-specific URL -->
        @if(isset($episode) && $episode)
        <div class="iframe-container">
            <iframe src="/tv/stream/{{ $tv->slug }}/season/{{ $season_number }}/episode/{{ $episode_number }}"
                allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            </iframe>
        </div>
        @else
        <!-- Episode not found - show error message -->
        <div class="iframe-container">
            <div class="bg-black w-full h-96 flex flex-col items-center justify-center text-white">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-6xl mb-4"></i>
                    <h2 class="text-2xl font-bold mb-2">Video Tidak Bisa Di Putar</h2>
                    <p class="text-sm text-gray-500">
                        Season {{ $season_number }}, Episode {{ $episode_number }}
                    </p>
                    <a href="/tv/{{ $tv->slug }}"
                        class="mt-4 inline-block bg-violet-600 hover:bg-violet-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Halaman TV Series
                    </a>
                </div>
            </div>
        </div>
        @endif
        @else
        <!-- Regular TV show URL -->
        <div class="iframe-container">
            <iframe src="/tv/stream/{{ $tv->slug }}" allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            </iframe>
        </div>
        @endif
    </div>
</section>

<!-- Section 1.5: Social Share -->
<section class="py-4 px-4 sm:px-6 lg:px-8 bg-gray-850 border-y border-gray-700">
    <div class="max-w-7xl mx-auto">
        <div>
            <div class="flex flex-wrap items-center justify-start gap-3">
                <!-- Social Media Share Buttons -->
                <div class="flex items-center gap-2">
                    <span class="text-gray-400 text-sm">Share:</span>
                    <div class="flex gap-2">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}&title={{ urlencode($tv->title) }}"
                            target="_blank"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                            title="Share on Facebook">
                            <i class="fab fa-facebook-f text-xs"></i>
                        </a>

                        <!-- Twitter -->
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode('Watch ' . $tv->title . ' on veenix') }}"
                            target="_blank"
                            class="bg-sky-500 hover:bg-sky-600 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                            title="Share on Twitter">
                            <i class="fab fa-twitter text-xs"></i>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://wa.me/?text={{ urlencode('Watch ' . $tv->title . ' - ' . url()->current()) }}"
                            target="_blank"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                            title="Share on WhatsApp">
                            <i class="fab fa-whatsapp text-xs"></i>
                        </a>

                        <!-- Telegram -->
                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode('Watch ' . $tv->title . ' on veenix') }}"
                            target="_blank"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                            title="Share on Telegram">
                            <i class="fab fa-telegram-plane text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Divider -->
                <div class="hidden sm:block w-px h-6 bg-gray-600"></div>

                <!-- Download Button -->
                {{-- <a href="/tv/download/{{ $tv->slug }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                    title="Download TV Series">
                    <i class="fas fa-download text-xs"></i>
                    <span class="hidden sm:inline">Download</span>
                </a> --}}

                <!-- Report Broken Link Button -->
                <button onclick="openReportModal()"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                    title="Report Broken Link">
                    <i class="fas fa-exclamation-triangle text-xs"></i>
                    <span class="hidden sm:inline">Report</span>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Report Modal -->
<div id="reportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl p-6 m-4 max-w-md w-full">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">Report Broken Link</h3>
            <button onclick="closeReportModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="reportForm" onsubmit="submitReport(event)">
            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-medium mb-2">TV Series Title</label>
                <input type="text" value="{{ $tv->title }}" readonly
                    class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg border border-gray-600 focus:border-violet-500 focus:outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-medium mb-2">Issue Type</label>
                <select name="issue_type" required
                    class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg border border-gray-600 focus:border-violet-500 focus:outline-none">
                    <option value="">Select an issue</option>
                    <option value="video_not_playing">Video cannot be played</option>
                    <option value="broken_link">Broken link</option>
                    <option value="poor_quality">Poor video quality</option>
                    <option value="audio_problem">Audio problem</option>
                    <option value="subtitle_issue">Subtitle issue</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-medium mb-2">Description</label>
                <textarea name="description" rows="3" required placeholder="Please describe the issue in detail..."
                    class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg border border-gray-600 focus:border-violet-500 focus:outline-none resize-none"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-medium mb-2">Your Email (optional)</label>
                <input type="email" name="email" placeholder="your@email.com"
                    class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg border border-gray-600 focus:border-violet-500 focus:outline-none">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Submit Report
                </button>
                <button type="button" onclick="closeReportModal()"
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Section 2: Seasons and Episodes -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-800">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold mb-8">Seasons & Episodes</h2>

        @if($tv->seasons && $tv->seasons->count() > 0)
        <div class="space-y-8">
            @foreach($tv->seasons as $season)
            <div class="bg-gray-700 rounded-xl overflow-hidden">
                <!-- Season Header -->
                <div class="p-6 border-b border-gray-600">
                    <!-- Desktop Layout -->
                    <div class="hidden sm:flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            @if($season->local_poster_path)
                            <img src="{{ asset('storage/' . $season->local_poster_path) }}" alt="{{ $season->name }}"
                                class="w-20 h-28 object-cover rounded-lg">
                            @elseif($season->poster_path)
                            <img src="https://image.tmdb.org/t/p/w185{{ $season->poster_path }}"
                                alt="{{ $season->name }}" class="w-20 h-28 object-cover rounded-lg">
                            @else
                            <div class="w-20 h-28 bg-gray-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tv text-gray-400 text-2xl"></i>
                            </div>
                            @endif

                            <div>
                                <h3 class="text-xl font-bold text-white">{{ $season->name }}</h3>
                                @if($season->air_date)
                                <p class="text-gray-400 text-sm">{{ \Carbon\Carbon::parse($season->air_date)->format('F
                                    j, Y') }}</p>
                                @endif
                                <p class="text-gray-400 text-sm">{{ $season->episode_count }} Episodes</p>
                                @if($season->vote_average)
                                <span class="flex items-center text-yellow-400 text-sm">
                                    <i class="fas fa-star mr-1"></i>
                                    {{ number_format($season->vote_average, 1) }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Episodes List on the right side -->
                        <div class="flex flex-wrap gap-2">
                            @if($season->episodes && $season->episodes->count() > 0)
                            @foreach($season->episodes->where('publish', true) as $episode)
                            <a href="/tv/{{ $tv->slug }}/season/{{ $season->season_number }}/episode/{{ $episode->episode_number }}"
                                class="w-10 h-10 bg-violet-600 hover:bg-violet-700 rounded-full flex items-center justify-center text-white font-semibold transition-colors @if(isset($episode_number) && $episode_number == $episode->episode_number && isset($season_number) && $season_number == $season->season_number) ring-2 ring-white ring-offset-2 ring-offset-violet-600 @endif"
                                title="Episode {{ $episode->episode_number }}: {{ $episode->name }}">
                                {{ $episode->episode_number }}
                            </a>
                            @endforeach
                            @else
                            <p class="text-gray-400 text-sm">No episodes</p>
                            @endif
                        </div>
                    </div>

                    <!-- Mobile Layout -->
                    <div class="sm:hidden">
                        <div class="flex items-center space-x-4 mb-4">
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
                                <h3 class="text-lg font-bold text-white">{{ $season->name }}</h3>
                                @if($season->air_date)
                                <p class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($season->air_date)->format('F
                                    j, Y') }}</p>
                                @endif
                                <p class="text-gray-400 text-xs">{{ $season->episode_count }} Episodes</p>
                                @if($season->vote_average)
                                <span class="flex items-center text-yellow-400 text-xs">
                                    <i class="fas fa-star mr-1"></i>
                                    {{ number_format($season->vote_average, 1) }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Episodes List for mobile -->
                        <div class="flex flex-wrap gap-2">
                            @if($season->episodes && $season->episodes->count() > 0)
                            @foreach($season->episodes->where('publish', true) as $episode)
                            <a href="/tv/{{ $tv->slug }}/season/{{ $season->season_number }}/episode/{{ $episode->episode_number }}"
                                class="w-8 h-8 bg-violet-600 hover:bg-violet-700 rounded-full flex items-center justify-center text-white text-sm font-semibold transition-colors @if(isset($episode_number) && $episode_number == $episode->episode_number && isset($season_number) && $season_number == $season->season_number) ring-2 ring-white ring-offset-2 ring-offset-violet-600 @endif"
                                title="Episode {{ $episode->episode_number }}: {{ $episode->name }}">
                                {{ $episode->episode_number }}
                            </a>
                            @endforeach
                            @else
                            <p class="text-gray-400 text-sm">No episodes</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-400">No seasons available for this TV series.</p>
        </div>
        @endif
    </div>
</section>

<!-- Section 3: TV Series Information -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-800">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- TV Series Poster -->
            <div class="lg:col-span-1">
                <img src="{{ $tv->poster_url }}" alt="{{ $tv->title }}" class="w-full rounded-xl shadow-2xl">
            </div>

            <!-- TV Series Details -->
            <div class="lg:col-span-2">
                <h1 class="text-4xl font-bold mb-4">{{ $tv->title }}</h1>

                <!-- TV Series Meta Info -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    @if($tv->first_air_date)
                    <span class="text-gray-300">{{ \Carbon\Carbon::parse($tv->first_air_date)->format('Y') }}
                        @if($tv->last_air_date && $tv->first_air_date->year != $tv->last_air_date->year)
                        - {{ \Carbon\Carbon::parse($tv->last_air_date)->format('Y') }}
                        @endif
                    </span>
                    @endif

                    @if($tv->status)
                    <span class="bg-violet-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                        {{ $tv->status }}
                    </span>
                    @endif

                    @if($tv->vote_average)
                    <span class="flex items-center text-yellow-400">
                        <i class="fas fa-star mr-1"></i>
                        {{ number_format($tv->vote_average, 1) }}
                    </span>
                    @endif

                    @if($tv->episode_run_time)
                    <span class="text-gray-300">{{ $tv->episode_run_time }} min</span>
                    @endif

                    @if($tv->number_of_seasons)
                    <span class="text-gray-300">{{ $tv->number_of_seasons }} Season{{ $tv->number_of_seasons > 1 ? 's' :
                        '' }}</span>
                    @endif
                </div>

                <!-- Genres -->
                @if($tv->genres_data && $tv->genres_data->count() > 0)
                <div class="mb-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($tv->genres_data as $genre)
                        <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full text-sm">
                            {{ $genre->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Overview -->
                @if($tv->overview)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Synopsis</h3>
                    <p class="text-gray-300 leading-relaxed">{{ $tv->overview }}</p>
                </div>
                @endif

                <!-- Countries -->
                @if($tv->countries && $tv->countries->count() > 0)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Countries</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tv->countries as $country)
                        <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full text-sm">
                            {{ $country->english_name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Cast -->
                @if($tv->actors && $tv->actors->count() > 0)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Cast</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($tv->actors->take(6) as $actor)
                        <div class="flex items-center space-x-3">
                            @if($actor->profile_path)
                            <img src="https://image.tmdb.org/t/p/w185{{ $actor->profile_path }}"
                                alt="{{ $actor->name }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                            <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-white">{{ $actor->name }}</p>
                                @if($actor->pivot->character)
                                <p class="text-xs text-gray-400">{{ $actor->pivot->character }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Related TV Series -->
<section class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold mb-8">Related TV Series</h2>

        @if($relatedTvs && $relatedTvs->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
            @foreach($relatedTvs as $relatedTv)
            <div class="movie-card cursor-pointer">
                <a href="/tv/{{ $relatedTv->slug }}">
                    <div class="relative overflow-hidden rounded-xl bg-gray-800 shadow-md">
                        <img src="{{ $relatedTv->poster_url }}" alt="{{ $relatedTv->title }}"
                            class="w-full h-auto aspect-[2/3] object-cover transition-transform duration-300 hover:scale-110">

                        <!-- Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300">
                        </div>

                        <!-- IMDB Rating -->
                        @if($relatedTv->vote_average)
                        <div
                            class="absolute top-2 left-2 bg-black/70 backdrop-blur-sm text-yellow-400 px-2 py-1 rounded-md text-xs font-bold flex items-center z-10">
                            <i class="fas fa-star mr-1"></i>
                            {{ number_format($relatedTv->vote_average, 1) }}
                        </div>
                        @endif

                        <!-- Status Badge -->
                        @if($relatedTv->status)
                        <div
                            class="absolute top-2 right-2 bg-violet-600/90 backdrop-blur-sm text-white px-2 py-1 rounded-md text-xs font-bold z-10">
                            {{ $relatedTv->status }}
                        </div>
                        @endif
                    </div>
                    <div class="mt-3 text-center">
                        <h3 class="font-semibold text-sm text-white truncate hover:text-violet-400 transition-colors">
                            {{ $relatedTv->title }}
                        </h3>
                        <div class="flex items-center justify-center space-x-3 text-gray-400 text-xs mt-1">
                            @if($relatedTv->first_air_date)
                            <span>{{ \Carbon\Carbon::parse($relatedTv->first_air_date)->format('Y') }}</span>
                            @endif
                            @if($relatedTv->number_of_seasons)
                            <span>{{ $relatedTv->number_of_seasons }} Season{{ $relatedTv->number_of_seasons > 1 ? 's' :
                                '' }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-400">No related TV series found.</p>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Report modal functionality
    function openReportModal() {
        document.getElementById('reportModal').classList.remove('hidden');
        document.getElementById('reportModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
        document.getElementById('reportModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
        document.getElementById('reportForm').reset();
    }

    function submitReport(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const reportData = {
            tv_id: '{{ $tv->id }}',
            tv_title: '{{ $tv->title }}',
            issue_type: formData.get('issue_type'),
            description: formData.get('description'),
            email: formData.get('email') || null
        };

        // Here you would typically send the data to your backend
        // For now, we'll just show a success message
        alert('Thank you for your report! We will look into this issue shortly.');
        closeReportModal();
    }

    // Close modal when clicking outside
    document.getElementById('reportModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeReportModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeReportModal();
        }
    });
</script>
@endpush
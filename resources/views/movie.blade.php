@extends('layouts.visitor')

@section('title')
{{ $movie->title }} - veenix
@endsection

@section('content')
<!-- Section 1: Video Player -->
<section class="bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="iframe-container">
            <iframe src="/movie/stream/{{ $movie->tmdb_id }}" allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
            </iframe>
        </div>


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
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}&title={{ urlencode($movie->title) }}"
                            target="_blank"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                            title="Share on Facebook">
                            <i class="fab fa-facebook-f text-xs"></i>
                        </a>

                        <!-- Twitter -->
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode('Watch ' . $movie->title . ' on veenix') }}"
                            target="_blank"
                            class="bg-sky-500 hover:bg-sky-600 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                            title="Share on Twitter">
                            <i class="fab fa-twitter text-xs"></i>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://wa.me/?text={{ urlencode('Watch ' . $movie->title . ' - ' . url()->current()) }}"
                            target="_blank"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                            title="Share on WhatsApp">
                            <i class="fab fa-whatsapp text-xs"></i>
                        </a>

                        <!-- Telegram -->
                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode('Watch ' . $movie->title . ' on veenix') }}"
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
                <a href="/movie/download/{{ $movie->slug }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1.5"
                    title="Download Movie">
                    <i class="fas fa-download text-xs"></i>
                    <span class="hidden sm:inline">Download</span>
                </a>

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
                <label class="block text-gray-300 text-sm font-medium mb-2">Movie Title</label>
                <input type="text" value="{{ $movie->title }}" readonly
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

<!-- Section 2: Movie Information -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-800">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Movie Poster -->
            <div class="lg:col-span-1">
                <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="w-full rounded-xl shadow-2xl">
            </div>

            <!-- Movie Details -->
            <div class="lg:col-span-2">
                <h1 class="text-4xl font-bold mb-4">{{ $movie->title }}</h1>

                <!-- Movie Meta Info -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    @if($movie->release_date)
                    <span class="text-gray-300">{{ \Carbon\Carbon::parse($movie->release_date)->format('Y')
                        }}</span>
                    @endif

                    @if($movie->quality)
                    <span class="bg-violet-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                        {{ $movie->quality }}
                    </span>
                    @endif

                    @if($movie->vote_average)
                    <span class="flex items-center text-yellow-400">
                        <i class="fas fa-star mr-1"></i>
                        {{ number_format($movie->vote_average, 1) }}
                    </span>
                    @endif

                    @if($movie->runtime)
                    <span class="text-gray-300">{{ $movie->runtime }} min</span>
                    @endif
                </div>

                <!-- Genres -->
                @if($movie->genres && $movie->genres->count() > 0)
                <div class="mb-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($movie->genres as $genre)
                        <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full text-sm">
                            {{ $genre->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Overview -->
                @if($movie->overview)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Synopsis</h3>
                    <p class="text-gray-300 leading-relaxed">{{ $movie->overview }}</p>
                </div>
                @endif

                <!-- Countries -->
                @if($movie->countries && $movie->countries->count() > 0)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Countries</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($movie->countries as $country)
                        <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full text-sm">
                            {{ $country->english_name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Cast -->
                @if($movie->actors && $movie->actors->count() > 0)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-3">Cast</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($movie->actors->take(6) as $actor)
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

<!-- Section 3: Related Movies -->
<section class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold mb-8">Related Movies</h2>

        @if($relatedMovies && $relatedMovies->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
            @foreach($relatedMovies as $relatedMovie)
            <div class="movie-card cursor-pointer">
                <a href="/movie/{{ $relatedMovie->slug }}">
                    <div class="relative overflow-hidden rounded-xl bg-gray-800 shadow-md">
                        <img src="{{ $relatedMovie->poster_url }}" alt="{{ $relatedMovie->title }}"
                            class="w-full h-auto aspect-[2/3] object-cover transition-transform duration-300 hover:scale-110">

                        <!-- Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300">
                        </div>

                        <!-- IMDB Rating -->
                        @if($relatedMovie->vote_average)
                        <div
                            class="absolute top-2 left-2 bg-black/70 backdrop-blur-sm text-yellow-400 px-2 py-1 rounded-md text-xs font-bold flex items-center z-10">
                            <i class="fas fa-star mr-1"></i>
                            {{ number_format($relatedMovie->vote_average, 1) }}
                        </div>
                        @endif

                        <!-- Quality Badge -->
                        @if($relatedMovie->quality)
                        <div
                            class="absolute top-2 right-2 bg-violet-600/90 backdrop-blur-sm text-white px-2 py-1 rounded-md text-xs font-bold z-10">
                            {{ $relatedMovie->quality }}
                        </div>
                        @endif
                    </div>
                    <div class="mt-3 text-center">
                        <h3 class="font-semibold text-sm text-white truncate hover:text-violet-400 transition-colors">
                            {{ $relatedMovie->title }}
                        </h3>
                        <div class="flex items-center justify-center space-x-3 text-gray-400 text-xs mt-1">
                            @if($relatedMovie->release_date)
                            <span>{{ \Carbon\Carbon::parse($relatedMovie->release_date)->format('Y') }}</span>
                            @endif
                            @if($relatedMovie->view_count)
                            <span class="flex items-center">
                                <i class="fas fa-eye mr-1"></i>
                                {{ $relatedMovie->view_count }}
                            </span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-400">No related movies found.</p>
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
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            
            if (!csrfToken) {
                alert('Security token not found. Please refresh the page and try again.');
                return;
            }
            
            const reportData = {
                movie_id: '{{ $movie->id }}',
                movie_title: '{{ $movie->title }}',
                issue_type: formData.get('issue_type'),
                description: formData.get('description'),
                email: formData.get('email') || null
            };

            // Send the data to backend
            fetch('/reports', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify(reportData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw data;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeReportModal();
                } else {
                    alert(data.message || 'Error submitting report. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Handle validation errors
                if (error.errors) {
                    let errorMessage = 'Please fix the following errors:\n\n';
                    for (const [field, messages] of Object.entries(error.errors)) {
                        errorMessage += `${field.charAt(0).toUpperCase() + field.slice(1).replace(/_/g, ' ')}: ${messages.join(', ')}\n`;
                    }
                    alert(errorMessage);
                } else if (error.message) {
                    alert(error.message);
                } else {
                    alert('Error submitting report. Please try again.');
                }
            });
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

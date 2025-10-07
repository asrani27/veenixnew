@extends('layouts.app')

@section('title', 'TV Series Seasons')

@section('content')
<div class="container mx-auto">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">TV Series Seasons</h3>
            <p class="text-gray-600 mt-1">{{ $tv->title ?? 'TV Series' }}</p>
        </div>
        <a href="{{ route('admin.tv.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to TV Series
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="p-6">
            <!-- TV Series Info -->
            <div class="flex flex-col md:flex-row gap-6 mb-8">
                <div class="md:w-48 flex-shrink-0">
                    <img src="{{ $tv->local_poster_path ? asset('storage/' . $tv->local_poster_path) : ($tv->poster_path ? 'https://image.tmdb.org/t/p/w300' . $tv->poster_path : asset('images/sample.jpg')) }}"
                        alt="{{ $tv->title ?? 'TV Series Image' }}" class="w-full rounded-lg shadow-md object-cover">
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $tv->title ?? 'Untitled' }}</h2>
                    <p class="text-gray-600 mb-4">{{ $tv->description ?? 'No description available' }}</p>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            ⭐ {{ number_format($tv->vote_average ?? 0, 1) }}
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $tv->episode_run_time ?? 'N/A' }} min
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            {{ $tv->status ?? 'N/A' }}
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            {{ $tv->number_of_seasons ?? 0 }} Seasons
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            {{ $tv->number_of_episodes ?? 0 }} Episodes
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-900">First Air Date:</span>
                            <p class="text-gray-600">{{ $tv->first_air_date ?
                                \Carbon\Carbon::parse($tv->first_air_date)->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Last Air Date:</span>
                            <p class="text-gray-600">{{ $tv->last_air_date ?
                                \Carbon\Carbon::parse($tv->last_air_date)->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Original Language:</span>
                            <p class="text-gray-600">{{ $tv->original_language ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Type:</span>
                            <p class="text-gray-600">{{ $tv->type ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($tv->genres_data && $tv->genres_data->count() > 0)
                    <div class="mt-4">
                        <span class="font-medium text-gray-900">Genres:</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($tv->genres_data as $genre)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $genre->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 mb-6">
                <button onclick="importSeasonsAndEpisodes('{{ $tv->id }}')"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-opacity-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                        </path>
                    </svg>
                    Import Seasons & Episodes
                </button>
                <a href="{{ route('admin.tv.edit', $tv->id) }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-opacity-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit TV Series
                </a>
            </div>

            <!-- Seasons Grid -->
            <div class="border-t pt-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Seasons</h3>

                @if($seasons->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @foreach($seasons as $season)
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <div class="aspect-[2/3] overflow-hidden bg-gray-100">
                            <img src="{{ $season->local_poster_path ? asset('storage/' . $season->local_poster_path) : ($season->poster_path ? 'https://image.tmdb.org/t/p/w300' . $season->poster_path : asset('images/sample.jpg')) }}"
                                alt="{{ $season->name ?? 'Season Image' }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-1">{{ $season->name ?? 'Season ' .
                                $season->season_number }}</h4>
                            <div class="space-y-1 text-xs text-gray-500">
                                <p>{{ $season->episode_count ?? 0 }} episodes</p>
                                <p>Air Date: {{ $season->air_date ? \Carbon\Carbon::parse($season->air_date)->format('M
                                    d, Y') : 'N/A' }}</p>
                                <p>⭐ {{ number_format($season->vote_average ?? 0, 1) }}</p>
                            </div>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('admin.tv.season.episodes', ['tv' => $tv->id, 'season' => $season->season_number]) }}"
                                    class="flex-1 px-3 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 text-xs font-medium rounded transition-colors text-center">
                                    View Episodes
                                </a>
                                <a href="{{ route('admin.tv.season.edit', ['tv' => $tv->id, 'season' => $season->season_number]) }}"
                                    class="flex-1 px-3 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-xs font-medium rounded transition-colors text-center">
                                    Edit Season
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No seasons found</h3>
                    <p class="mt-1 text-sm text-gray-500">Import seasons and episodes to get started.</p>
                    <div class="mt-6">
                        <button onclick="importSeasonsAndEpisodes('{{ $tv->id }}')"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                </path>
                            </svg>
                            Import Seasons & Episodes
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function importSeasonsAndEpisodes(tvId) {
        if (confirm('Are you sure you want to import all seasons and episodes for this TV series? This may take a while.')) {
            // Show loading state
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Importing...';
            button.disabled = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page and try again.');
                button.innerHTML = originalHTML;
                button.disabled = false;
                return;
            }
            
            fetch(`/admin/tv/${tvId}/import-seasons`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Success: ${data.message}`);
                    // Reload page to show updated data
                    location.reload();
                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while importing seasons and episodes.');
            })
            .finally(() => {
                // Restore button state
                button.innerHTML = originalHTML;
                button.disabled = false;
            });
        }
    }
</script>
@endpush
@endsection
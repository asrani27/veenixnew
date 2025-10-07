@extends('layouts.app')

@section('title', 'Create Episode')

@section('header_title', 'Create Episode')

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Create Episode</h3>
        <p class="text-gray-600 mt-1">Add a new episode to {{ $tv->title }} - Season {{ $season->season_number }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-3 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Episode Information</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.tv.episode.store', ['tv' => $tv->id, 'season' => $season->season_number]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Import from TMDB Section -->
                <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b border-blue-200 pb-2">
                        <svg class="w-5 h-5 inline mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import from TMDB
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label for="import_episode_number" class="block text-sm font-medium text-gray-700 mb-1">Episode Number</label>
                            <input type="number" id="import_episode_number" min="1" placeholder="e.g., 24"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <button type="button" id="importEpisodeBtn" 
                                class="w-full inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Import Episode
                            </button>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p class="font-medium">How it works:</p>
                            <p>Enter episode number and click import to fetch data from TMDB automatically.</p>
                        </div>
                    </div>
                    <div id="importStatus" class="mt-4 hidden">
                        <!-- Status messages will appear here -->
                    </div>
                </div>

                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Basic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Episode Title *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="air_date" class="block text-sm font-medium text-gray-700 mb-1">Air Date</label>
                            <input type="date" name="air_date" id="air_date" value="{{ old('air_date') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('air_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="runtime" class="block text-sm font-medium text-gray-700 mb-1">Runtime (minutes)</label>
                            <input type="number" name="runtime" id="runtime" value="{{ old('runtime') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="0">
                            @error('runtime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="vote_average" class="block text-sm font-medium text-gray-700 mb-1">Vote Average (0-10)</label>
                            <input type="number" name="vote_average" id="vote_average" value="{{ old('vote_average') }}" step="0.1" min="0" max="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('vote_average')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="vote_count" class="block text-sm font-medium text-gray-700 mb-1">Vote Count</label>
                            <input type="number" name="vote_count" id="vote_count" value="{{ old('vote_count') }}" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('vote_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Description</h4>
                    <div>
                        <label for="overview" class="block text-sm font-medium text-gray-700 mb-1">Overview</label>
                        <textarea name="overview" id="overview" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('overview') }}</textarea>
                        @error('overview')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Media Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Episode Still Image</h4>
                    <div>
                        <label for="still" class="block text-sm font-medium text-gray-700 mb-1">Episode Still (Thumbnail)</label>
                        <input type="file" name="still" id="still" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Recommended size: 1280x720px. Max size: 2MB. Formats: JPG, PNG, GIF.</p>
                        @error('still')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-between items-center border-t pt-6">
                    <a href="{{ route('admin.tv.episodes-page', ['tv' => $tv->id, 'season' => $season->season_number]) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Episode
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const importEpisodeBtn = document.getElementById('importEpisodeBtn');
    const importEpisodeNumber = document.getElementById('import_episode_number');
    const importStatus = document.getElementById('importStatus');
    const tvId = '{{ $tv->id }}';
    const seasonNumber = {{ $season->season_number }};

    importEpisodeBtn.addEventListener('click', function() {
        const episodeNumber = importEpisodeNumber.value.trim();
        
        if (!episodeNumber) {
            showImportStatus('Please enter an episode number', 'error');
            return;
        }

        if (episodeNumber < 1) {
            showImportStatus('Episode number must be greater than 0', 'error');
            return;
        }

        // Disable button and show loading
        importEpisodeBtn.disabled = true;
        importEpisodeBtn.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Importing...';

        // Make AJAX request to import episode
        fetch(`/admin/tv/${tvId}/season/${seasonNumber}/episode/import`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                episode_number: parseInt(episodeNumber)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showImportStatus(data.message, 'success');
                
                // Redirect to episodes page after successful import
                setTimeout(() => {
                    window.location.href = data.data.redirect_url;
                }, 2000);
            } else {
                showImportStatus(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showImportStatus('An error occurred while importing the episode', 'error');
        })
        .finally(() => {
            // Re-enable button
            importEpisodeBtn.disabled = false;
            importEpisodeBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>Import Episode';
        });
    });

    function showImportStatus(message, type) {
        importStatus.classList.remove('hidden');
        
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
        const icon = type === 'success' 
            ? '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            : '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        
        importStatus.className = 'mt-4 p-4 rounded-lg border ' + bgColor;
        importStatus.innerHTML = icon + message;
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                importStatus.classList.add('hidden');
            }, 5000);
        }
    }

    // Allow Enter key to trigger import
    importEpisodeNumber.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            importEpisodeBtn.click();
        }
    });
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Edit TV Series')

@section('header_title', 'Edit TV Series')

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Edit TV Series</h3>
        <p class="text-gray-600 mt-1">Update the TV series information below.</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-3 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">TV Series Information</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.tv.update', $tv->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Basic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $tv->title) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="original_title" class="block text-sm font-medium text-gray-700 mb-1">Original
                                Title</label>
                            <input type="text" name="original_title" id="original_title"
                                value="{{ old('original_title', $tv->original_title) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('original_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <input type="text" name="type" id="type" value="{{ old('type', $tv->type) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Returning Series" {{ old('status', $tv->status) == 'Returning Series' ?
                                    'selected' : '' }}>Returning Series</option>
                                <option value="Planned" {{ old('status', $tv->status) == 'Planned' ? 'selected' : ''
                                    }}>Planned</option>
                                <option value="In Production" {{ old('status', $tv->status) == 'In Production' ?
                                    'selected' : '' }}>In Production</option>
                                <option value="Pilot" {{ old('status', $tv->status) == 'Pilot' ? 'selected' : ''
                                    }}>Pilot</option>
                                <option value="Canceled" {{ old('status', $tv->status) == 'Canceled' ? 'selected' : ''
                                    }}>Canceled</option>
                                <option value="Ended" {{ old('status', $tv->status) == 'Ended' ? 'selected' : ''
                                    }}>Ended</option>
                            </select>
                            @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="first_air_date" class="block text-sm font-medium text-gray-700 mb-1">First Air
                                Date</label>
                            <input type="date" name="first_air_date" id="first_air_date"
                                value="{{ old('first_air_date', $tv->first_air_date) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('first_air_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="last_air_date" class="block text-sm font-medium text-gray-700 mb-1">Last Air
                                Date</label>
                            <input type="date" name="last_air_date" id="last_air_date"
                                value="{{ old('last_air_date', $tv->last_air_date) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('last_air_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Relationships Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Genres, Countries & Actors</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="genres" class="block text-sm font-medium text-gray-700 mb-1">Genres</label>
                            <select name="genres[]" id="genres" multiple>
                                @foreach (\App\Models\Genre::orderBy('name')->get() as $genre)
                                <option value="{{ $genre->id }}" {{ $tv->genres_data->contains($genre->id) ? 'selected'
                                    : '' }}>
                                    {{ $genre->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="countries" class="block text-sm font-medium text-gray-700 mb-1">Production
                                Countries</label>
                            <select name="countries[]" id="countries" multiple>
                                @foreach (\App\Models\Country::orderBy('english_name')->get() as $country)
                                <option value="{{ $country->id }}" {{ $tv->countries->contains($country->id) ?
                                    'selected' : '' }}>
                                    {{ $country->english_name }} ({{ $country->iso_3166_1 }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="actors" class="block text-sm font-medium text-gray-700 mb-1">Actors</label>
                            <select name="actors[]" id="actors" multiple>
                                @foreach (\App\Models\Actor::orderBy('name')->get() as $actor)
                                <option value="{{ $actor->id }}" {{ $tv->actors->contains($actor->id) ? 'selected' : ''
                                    }}>
                                    {{ $actor->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Descriptions Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Descriptions</h4>
                    <div class="space-y-6">
                        <div>
                            <label for="overview" class="block text-sm font-medium text-gray-700 mb-1">Overview</label>
                            <textarea name="overview" id="overview" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('overview', $tv->overview) }}</textarea>
                            @error('overview')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $tv->description) }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tagline" class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                            <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $tv->tagline) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('tagline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="homepage" class="block text-sm font-medium text-gray-700 mb-1">Homepage</label>
                            <input type="url" name="homepage" id="homepage" value="{{ old('homepage', $tv->homepage) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('homepage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- TMDB Data Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">TMDB Data</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="tmdb_id" class="block text-sm font-medium text-gray-700 mb-1">TMDB ID</label>
                            <input type="text" name="tmdb_id" id="tmdb_id" value="{{ old('tmdb_id', $tv->tmdb_id) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                readonly>
                            @error('tmdb_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="episode_run_time" class="block text-sm font-medium text-gray-700 mb-1">Episode
                                Runtime (minutes)</label>
                            <input type="number" name="episode_run_time" id="episode_run_time"
                                value="{{ old('episode_run_time', $tv->episode_run_time) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('episode_run_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="number_of_seasons" class="block text-sm font-medium text-gray-700 mb-1">Number
                                of Seasons</label>
                            <input type="number" name="number_of_seasons" id="number_of_seasons"
                                value="{{ old('number_of_seasons', $tv->number_of_seasons) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('number_of_seasons')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="number_of_episodes" class="block text-sm font-medium text-gray-700 mb-1">Number
                                of Episodes</label>
                            <input type="number" name="number_of_episodes" id="number_of_episodes"
                                value="{{ old('number_of_episodes', $tv->number_of_episodes) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('number_of_episodes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="original_language" class="block text-sm font-medium text-gray-700 mb-1">Original
                                Language</label>
                            <input type="text" name="original_language" id="original_language"
                                value="{{ old('original_language', $tv->original_language) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                maxlength="10">
                            @error('original_language')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="in_production" class="block text-sm font-medium text-gray-700 mb-1">In
                                Production</label>
                            <select name="in_production" id="in_production"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="0" {{ old('in_production', $tv->in_production) == 0 ? 'selected' : ''
                                    }}>No</option>
                                <option value="1" {{ old('in_production', $tv->in_production) == 1 ? 'selected' : ''
                                    }}>Yes</option>
                            </select>
                            @error('in_production')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Ratings Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Ratings & Statistics</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="vote_average" class="block text-sm font-medium text-gray-700 mb-1">Vote Average
                                (0-10)</label>
                            <input type="number" name="vote_average" id="vote_average"
                                value="{{ old('vote_average', $tv->vote_average) }}" step="0.1" min="0" max="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('vote_average')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="vote_count" class="block text-sm font-medium text-gray-700 mb-1">Vote
                                Count</label>
                            <input type="number" name="vote_count" id="vote_count"
                                value="{{ old('vote_count', $tv->vote_count) }}" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('vote_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="popularity"
                                class="block text-sm font-medium text-gray-700 mb-1">Popularity</label>
                            <input type="number" name="popularity" id="popularity"
                                value="{{ old('popularity', $tv->popularity) }}" step="0.01" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('popularity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Media Paths Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Media Paths</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="poster_path" class="block text-sm font-medium text-gray-700 mb-1">Poster Path
                                (TMDB)</label>
                            <input type="text" name="poster_path" id="poster_path"
                                value="{{ old('poster_path', $tv->poster_path) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('poster_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="backdrop_path" class="block text-sm font-medium text-gray-700 mb-1">Backdrop
                                Path (TMDB)</label>
                            <input type="text" name="backdrop_path" id="backdrop_path"
                                value="{{ old('backdrop_path', $tv->backdrop_path) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('backdrop_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="local_poster_path" class="block text-sm font-medium text-gray-700 mb-1">Local
                                Poster Path</label>
                            <input type="text" name="local_poster_path" id="local_poster_path"
                                value="{{ old('local_poster_path', $tv->local_poster_path) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('local_poster_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="local_backdrop_path" class="block text-sm font-medium text-gray-700 mb-1">Local
                                Backdrop Path</label>
                            <input type="text" name="local_backdrop_path" id="local_backdrop_path"
                                value="{{ old('local_backdrop_path', $tv->local_backdrop_path) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('local_backdrop_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-between items-center border-t pt-6">
                    <a href="{{ route('admin.tv.seasons-page', $tv->id) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update TV Series
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Hide original select elements */
    select[multiple] {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof SlimSelect === 'undefined') {
            return;
        }
        
        const slimSelectConfig = {
            placeholder: 'Select items...',
            allowDeselect: true,
            deselectLabel: '<span class="ss-deselect-label">×</span>',
            closeOnSelect: false,
            searchPlaceholder: 'Search items...'
        };
        
        const selectors = ['#genres', '#countries', '#actors'];
        const placeholders = ['Select genres...', 'Select countries...', 'Select actors...'];
        const searchPlaceholders = ['Search genres...', 'Search countries...', 'Search actors...'];
        
        selectors.forEach(function(selector, index) {
            try {
                const config = { ...slimSelectConfig };
                config.select = selector;
                config.placeholder = placeholders[index];
                config.searchPlaceholder = searchPlaceholders[index];
                
                if (document.querySelector(selector)) {
                    new SlimSelect(config);
                }
            } catch (e) {
                console.error('Error initializing Slim Select for ' + selector + ':', e);
            }
        });
    });
</script>
@endpush
@extends('layouts.app')

@section('title', 'Edit Movie')

@section('header_title', 'Edit Movie')

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Edit Movie</h3>
        <p class="text-gray-600 mt-1">Update the movie information below.</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-3 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Movie Information</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Basic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $movie->title) }}"
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
                                value="{{ old('original_title', $movie->original_title) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('original_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <input type="text" name="category" id="category"
                                value="{{ old('category', $movie->category) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                            <input type="number" name="year" id="year" value="{{ old('year', $movie->year) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="release_date" class="block text-sm font-medium text-gray-700 mb-1">Release
                                Date</label>
                            <input type="date" name="release_date" id="release_date"
                                value="{{ old('release_date', $movie->release_date) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('release_date')
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
                                <option value="{{ $genre->id }}" {{ $movie->genres->contains($genre->id) ? 'selected' :
                                    '' }}>
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
                                <option value="{{ $country->id }}" {{ $movie->countries->contains($country->id) ?
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
                                <option value="{{ $actor->id }}" {{ $movie->actors->contains($actor->id) ? 'selected' :
                                    '' }}>
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('overview', $movie->overview) }}</textarea>
                            @error('overview')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $movie->description) }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tagline" class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                            <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $movie->tagline) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('tagline')
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
                            <input type="text" name="tmdb_id" id="tmdb_id" value="{{ old('tmdb_id', $movie->tmdb_id) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                readonly>
                            @error('tmdb_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="runtime" class="block text-sm font-medium text-gray-700 mb-1">Runtime
                                (minutes)</label>
                            <input type="number" name="runtime" id="runtime"
                                value="{{ old('runtime', $movie->runtime) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('runtime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Released" {{ old('status', $movie->status) == 'Released' ? 'selected' :
                                    '' }}>Released</option>
                                <option value="Post Production" {{ old('status', $movie->status) == 'Post Production' ?
                                    'selected' : '' }}>Post Production</option>
                                <option value="In Production" {{ old('status', $movie->status) == 'In Production' ?
                                    'selected' : '' }}>In Production</option>
                                <option value="Planned" {{ old('status', $movie->status) == 'Planned' ? 'selected' : ''
                                    }}>Planned</option>
                                <option value="Canceled" {{ old('status', $movie->status) == 'Canceled' ? 'selected' :
                                    '' }}>Canceled</option>
                            </select>
                            @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="original_language" class="block text-sm font-medium text-gray-700 mb-1">Original
                                Language</label>
                            <input type="text" name="original_language" id="original_language"
                                value="{{ old('original_language', $movie->original_language) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                maxlength="10">
                            @error('original_language')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="adult" class="block text-sm font-medium text-gray-700 mb-1">Adult
                                Content</label>
                            <select name="adult" id="adult"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="0" {{ old('adult', $movie->adult) == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('adult', $movie->adult) == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('adult')
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
                                value="{{ old('vote_average', $movie->vote_average) }}" step="0.1" min="0" max="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('vote_average')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="vote_count" class="block text-sm font-medium text-gray-700 mb-1">Vote
                                Count</label>
                            <input type="number" name="vote_count" id="vote_count"
                                value="{{ old('vote_count', $movie->vote_count) }}" min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('vote_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="popularity"
                                class="block text-sm font-medium text-gray-700 mb-1">Popularity</label>
                            <input type="number" name="popularity" id="popularity"
                                value="{{ old('popularity', $movie->popularity) }}" step="0.01" min="0"
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
                                value="{{ old('poster_path', $movie->poster_path) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('poster_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="backdrop_path" class="block text-sm font-medium text-gray-700 mb-1">Backdrop
                                Path (TMDB)</label>
                            <input type="text" name="backdrop_path" id="backdrop_path"
                                value="{{ old('backdrop_path', $movie->backdrop_path) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('backdrop_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="local_poster_path" class="block text-sm font-medium text-gray-700 mb-1">Local
                                Poster Path</label>
                            <input type="text" name="local_poster_path" id="local_poster_path"
                                value="{{ old('local_poster_path', $movie->local_poster_path) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('local_poster_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="local_backdrop_path" class="block text-sm font-medium text-gray-700 mb-1">Local
                                Backdrop Path</label>
                            <input type="text" name="local_backdrop_path" id="local_backdrop_path"
                                value="{{ old('local_backdrop_path', $movie->local_backdrop_path) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('local_backdrop_path')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Download Links Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Download Links</h4>
                    <div class="space-y-4">
                        <!-- Existing Download Links -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Existing Download Links</label>
                            @if($movie->downloadLinks && $movie->downloadLinks->count() > 0)
                            <div class="space-y-2" id="existing-download-links">
                                @foreach($movie->downloadLinks as $index => $link)
                                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg download-link-item">
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-5 gap-2">
                                        <input type="text" name="download_links[{{ $index }}][url]"
                                            value="{{ $link->url }}" placeholder="Download URL"
                                            class="md:col-span-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <select name="download_links[{{ $index }}][quality]"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                            <option value="540p" {{ $link->quality == '540p' ? 'selected' : '' }}>540p
                                            </option>
                                            <option value="720p" {{ $link->quality == '720p' ? 'selected' : '' }}>720p
                                            </option>
                                        </select>
                                        <input type="text" name="download_links[{{ $index }}][label]"
                                            value="{{ $link->label }}" placeholder="Label (optional)"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <input type="number" name="download_links[{{ $index }}][sort_order]"
                                            value="{{ $link->sort_order }}" placeholder="Order" min="0"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="download_links[{{ $index }}][is_active]"
                                                value="1" {{ $link->is_active ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300
                                            focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-1 text-sm text-gray-700">Active</span>
                                        </label>
                                        <button type="button" onclick="removeDownloadLink(this)"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Remove
                                        </button>
                                    </div>
                                    <input type="hidden" name="download_links[{{ $index }}][id]"
                                        value="{{ $link->id }}">
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-gray-500 text-sm">No download links added yet.</p>
                            @endif
                        </div>

                        <!-- Add New Download Links -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Add New Download Links</label>
                            <div id="new-download-links" class="space-y-2">
                                <!-- New links will be added here -->
                            </div>
                            <button type="button" onclick="addNewDownloadLink()"
                                class="mt-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>Add Download Link
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-between items-center border-t pt-6">
                    <a href="{{ route('admin.movies.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Movie
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
    // Global functions for download links management
    let newDownloadLinkIndex = {{ $movie->downloadLinks ? $movie->downloadLinks->count() : 0 }};

    function addNewDownloadLink() {
        const container = document.getElementById('new-download-links');
        const linkDiv = document.createElement('div');
        linkDiv.className = 'flex items-center gap-2 p-3 bg-gray-50 rounded-lg download-link-item';
        
        linkDiv.innerHTML = `
            <div class="flex-1 grid grid-cols-1 md:grid-cols-5 gap-2">
                <input type="text" 
                       name="download_links[new_${newDownloadLinkIndex}][url]" 
                       placeholder="Download URL"
                       class="md:col-span-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <select name="download_links[new_${newDownloadLinkIndex}][quality]" 
                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="540p">540p</option>
                    <option value="720p">720p</option>
                </select>
                <input type="text" 
                       name="download_links[new_${newDownloadLinkIndex}][label]" 
                       placeholder="Label (optional)"
                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <input type="number" 
                       name="download_links[new_${newDownloadLinkIndex}][sort_order]" 
                       value="${newDownloadLinkIndex}"
                       placeholder="Order"
                       min="0"
                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="download_links[new_${newDownloadLinkIndex}][is_active]" 
                           value="1" 
                           checked
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-1 text-sm text-gray-700">Active</span>
                </label>
                <button type="button" 
                        onclick="removeDownloadLink(this)" 
                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                    Remove
                </button>
            </div>
        `;
        
        container.appendChild(linkDiv);
        newDownloadLinkIndex++;
    }

    function removeDownloadLink(button) {
        const linkItem = button.closest('.download-link-item');
        if (linkItem) {
            linkItem.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize SlimSelect
        if (typeof SlimSelect !== 'undefined') {
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
        }


    });
</script>
@endpush
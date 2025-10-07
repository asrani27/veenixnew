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

                <!-- Video File Section -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4 border-b pb-2">Video File</h4>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File Video
                                (Filename In Your Storage)</label>
                            <input type="text" name="file" id="file" value="{{ old('file', $movie->file) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Resumable.js Upload Section -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Video File</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <div id="resumable-dropzone" class="cursor-pointer">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <span class="font-medium">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500">MP4, AVI, MKV, MOV, WMV, FLV, WebM up to 2GB</p>
                                    </div>
                                    <input id="resumable-file-input" type="file" class="hidden" accept="video/*">
                                </div>
                                
                                <!-- Upload Progress -->
                                <div id="upload-progress" class="hidden mt-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700">Uploading...</span>
                                        <span id="upload-percentage" class="text-sm text-gray-600">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div id="upload-progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="upload-status" class="text-sm text-gray-600 mt-2"></p>
                                </div>
                                
                                <!-- Upload Success -->
                                <div id="upload-success" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-green-800">Upload completed successfully!</span>
                                    </div>
                                    <p id="upload-file-info" class="text-sm text-green-700 mt-1"></p>
                                </div>
                                
                                <!-- Upload Error -->
                                <div id="upload-error" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-sm font-medium text-red-800">Upload failed</span>
                                    </div>
                                    <p id="upload-error-message" class="text-sm text-red-700 mt-1"></p>
                                </div>
                                
                            </div>
                            
                            <br />
                            <br />
                            <a href="/movie/stream/{{$movie->tmdb_id}}" target="_blank"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Stream
                            </a>
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
<script src="{{ asset('js/resumable.js') }}"></script>
<script>
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

        // Initialize Resumable.js
        if (typeof Resumable !== 'undefined') {
            const r = new Resumable({
                target: '{{ route("upload.resumable.upload") }}',
                chunkSize: 2 * 1024 * 1024, // 2MB chunks
                simultaneousUploads: 3,
                testChunks: true,
                throttleProgressCallbacks: 1,
                query: {
                    _token: '{{ csrf_token() }}',
                    movie_id: '{{ $movie->id }}'
                }
            });

            // UI Elements
            const dropzone = document.getElementById('resumable-dropzone');
            const fileInput = document.getElementById('resumable-file-input');
            const progressDiv = document.getElementById('upload-progress');
            const progressBar = document.getElementById('upload-progress-bar');
            const percentageSpan = document.getElementById('upload-percentage');
            const statusP = document.getElementById('upload-status');
            const successDiv = document.getElementById('upload-success');
            const fileInfoP = document.getElementById('upload-file-info');
            const errorDiv = document.getElementById('upload-error');
            const errorMessageP = document.getElementById('upload-error-message');
            const fileInputField = document.getElementById('file');

            // Show/hide functions
            function showProgress() {
                progressDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                errorDiv.classList.add('hidden');
            }

            function showSuccess(filename, size) {
                progressDiv.classList.add('hidden');
                errorDiv.classList.add('hidden');
                successDiv.classList.remove('hidden');
                fileInfoP.textContent = `${filename} (${formatFileSize(size)})`;
            }

            function showError(message) {
                progressDiv.classList.add('hidden');
                successDiv.classList.add('hidden');
                errorDiv.classList.remove('hidden');
                errorMessageP.textContent = message;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Resumable.js events
            r.on('fileAdded', function(file) {
                showProgress();
                statusP.textContent = `Preparing upload: ${file.fileName}`;
                r.upload();
            });

            r.on('progress', function() {
                const progress = r.progress() * 100;
                progressBar.style.width = progress + '%';
                percentageSpan.textContent = Math.round(progress) + '%';
                statusP.textContent = `Uploading... ${Math.round(progress)}% complete`;
            });

            r.on('fileSuccess', function(file, message) {
                try {
                    const response = JSON.parse(message);
                    if (response.success) {
                        showSuccess(response.filename, response.size);
                        // Update the file input field with the uploaded filename
                        fileInputField.value = response.filename;
                        statusP.textContent = 'Upload completed successfully! HLS conversion will start in the background.';
                    } else {
                        showError(response.error || 'Upload failed');
                    }
                } catch (e) {
                    showError('Invalid server response');
                }
            });

            r.on('fileError', function(file, message) {
                try {
                    const response = JSON.parse(message);
                    showError(response.error || message || 'Upload failed');
                } catch (e) {
                    showError(message || 'Upload failed');
                }
            });

            r.on('pause', function() {
                statusP.textContent = 'Upload paused';
            });

            r.on('resume', function() {
                statusP.textContent = 'Resuming upload...';
            });

            // Drag and drop
            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropzone.classList.add('border-blue-500', 'bg-blue-50');
            });

            dropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            });

            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropzone.classList.remove('border-blue-500', 'bg-blue-50');
                
                const files = e.dataTransfer.files;
                for (let i = 0; i < files.length; i++) {
                    r.addFile(files[i]);
                }
            });

            // Click to upload
            dropzone.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                const files = e.target.files;
                for (let i = 0; i < files.length; i++) {
                    r.addFile(files[i]);
                }
                // Clear the input so the same file can be selected again
                e.target.value = '';
            });

            // Assign dropzone to Resumable.js
            r.assignDrop(dropzone);
            r.assignBrowse(fileInput);

            console.log('Resumable.js initialized successfully');
        } else {
            console.error('Resumable.js not loaded');
        }

    });
</script>
@endpush

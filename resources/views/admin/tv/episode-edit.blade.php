@extends('layouts.app')

@section('title', 'Edit Episode - ' . $tv->title)

@section('content')
<div class="container mx-auto">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Edit Episode</h3>
            <p class="text-gray-600 mt-1">{{ $tv->title }} - Season {{ $season->season_number }} - Episode {{
                $episode->episode_number }}</p>
        </div>
        <a href="{{ route('admin.tv.season.episodes', ['tv' => $tv->id, 'season' => $season->season_number]) }}"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Episodes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="p-6">
            <form
                action="{{ route('admin.tv.episode.update', ['tv' => $tv->id, 'season' => $season->season_number, 'episode' => $episode->episode_number]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Form Fields -->
                    <div class="space-y-6">
                        <!-- Episode Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Episode Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $episode->name) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Overview -->
                        <div>
                            <label for="overview" class="block text-sm font-medium text-gray-700 mb-2">Overview</label>
                            <textarea id="overview" name="overview" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('overview', $episode->overview) }}</textarea>
                            @error('overview')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Air Date -->
                        <div>
                            <label for="air_date" class="block text-sm font-medium text-gray-700 mb-2">Air Date</label>
                            <input type="date" id="air_date" name="air_date"
                                value="{{ old('air_date', $episode->air_date) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('air_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Runtime -->
                        <div>
                            <label for="runtime" class="block text-sm font-medium text-gray-700 mb-2">Runtime
                                (minutes)</label>
                            <input type="number" id="runtime" name="runtime"
                                value="{{ old('runtime', $episode->runtime) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="0">
                            @error('runtime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Vote Average -->
                        <div>
                            <label for="vote_average" class="block text-sm font-medium text-gray-700 mb-2">Vote Average
                                (0-10)</label>
                            <input type="number" id="vote_average" name="vote_average"
                                value="{{ old('vote_average', $episode->vote_average) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                step="0.1" min="0" max="10">
                            @error('vote_average')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Vote Count -->
                        <div>
                            <label for="vote_count" class="block text-sm font-medium text-gray-700 mb-2">Vote
                                Count</label>
                            <input type="number" id="vote_count" name="vote_count"
                                value="{{ old('vote_count', $episode->vote_count) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="0">
                            @error('vote_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column - Still Upload and Preview -->
                    <div class="space-y-6">
                        <!-- Still Image Upload -->
                        <div>
                            <label for="still" class="block text-sm font-medium text-gray-700 mb-2">Episode Still
                                Image</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="still"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a new still image</span>
                                            <input id="still" name="still" type="file" class="sr-only"
                                                accept="image/jpeg,image/png,image/jpg,image/gif">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            @error('still')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Still Preview -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Still Image</label>
                            <div class="flex justify-center">
                                <div class="w-64 h-36 bg-gray-100 rounded-lg overflow-hidden shadow-md">
                                    <img src="{{ $episode->local_still_path ? asset('storage/' . $episode->local_still_path) : ($episode->still_path ? 'https://image.tmdb.org/t/p/w300' . $episode->still_path : asset('images/sample.jpg')) }}"
                                        alt="{{ $episode->name ?? 'Episode ' . $episode->episode_number }}"
                                        class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>

                        <!-- Episode Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Episode Information</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p><span class="font-medium">Episode Number:</span> {{ $episode->episode_number }}</p>
                                <p><span class="font-medium">Season Number:</span> {{ $episode->season_number }}</p>
                                <p><span class="font-medium">TMDB ID:</span> {{ $episode->tmdb_id ?? 'N/A' }}</p>
                                <p><span class="font-medium">Created:</span> {{ $episode->created_at->format('M d, Y
                                    H:i') }}</p>
                                <p><span class="font-medium">Updated:</span> {{ $episode->updated_at->format('M d, Y
                                    H:i') }}</p>
                            </div>
                        </div>

                        <!-- Season Info -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Season Information</h4>
                            <div class="space-y-1 text-sm text-blue-700">
                                <p><span class="font-medium">Season Name:</span> {{ $season->name }}</p>
                                <p><span class="font-medium">Season Number:</span> {{ $season->season_number }}</p>
                                <p><span class="font-medium">Air Date:</span> {{ $season->air_date ?
                                    \Carbon\Carbon::parse($season->air_date)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Video File Upload -->
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Episode Video File</label>
                            <input type="text" name="file" id="file" value="{{ old('file', $episode->file) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="video filename in storage">
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
                            
                            <!-- Stream Button -->
                            @if($episode->file)
                            <div class="mt-4">
                                <a href="{{ route('tv.episode.stream', ['slug' => $episode->tv->slug, 'season_number' => $episode->season_number, 'episode_number' => $episode->episode_number]) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Stream Episode
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.tv.season.episodes', ['tv' => $tv->id, 'season' => $season->season_number]) }}"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-opacity-50">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V2">
                            </path>
                        </svg>
                        Update Episode
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ asset('js/resumable.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Resumable.js for episode video upload
    if (typeof Resumable !== 'undefined') {
        const r = new Resumable({
            target: '{{ route("admin.episodes.upload-video", $episode->id) }}',
            chunkSize: 2 * 1024 * 1024, // 2MB chunks
            simultaneousUploads: 3,
            testChunks: true,
            throttleProgressCallbacks: 1,
            query: {
                _token: '{{ csrf_token() }}'
            },
            testMethod: 'GET',
            uploadMethod: 'POST'
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
                    showSuccess(response.filename, response.size || file.file.size);
                    // Update the file input field with the uploaded filename
                    fileInputField.value = response.path || response.filename;
                    statusP.textContent = 'Upload completed successfully! HLS conversion started...';
                    
                    // Show success message that HLS conversion has started
                    setTimeout(() => {
                        statusP.textContent = 'Video uploaded successfully! HLS conversion is running in the background.';
                    }, 2000);
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

        console.log('Resumable.js initialized successfully for episode upload');
    } else {
        console.error('Resumable.js not loaded');
    }
});
</script>
@endpush
@endsection

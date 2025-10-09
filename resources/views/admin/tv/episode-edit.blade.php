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

                        <!-- Download Links -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Download Links</label>
                            <div id="downloadLinksContainer">
                                <div class="space-y-4" id="downloadLinksList">
                                    <!-- Download links will be added here dynamically -->
                                </div>
                                <button type="button" id="addDownloadLinkBtn" 
                                    class="mt-4 inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Download Link
                                </button>
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
                            
                            <!-- TUS Upload Section -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Video File</label>
                                <div id="uploadArea"
                                    class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200 bg-gray-50 hover:bg-blue-50 cursor-pointer">
                                    <input type="file" id="fileInput" class="hidden" accept="video/*" />

                                    <!-- Upload Icon -->
                                    <div class="mb-4">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                    </div>

                                    <!-- Upload Text -->
                                    <div class="mb-4">
                                        <p class="text-lg font-medium text-gray-900 mb-2">Drag & drop video file here</p>
                                        <p class="text-sm text-gray-500">or click to browse</p>
                                        <p class="text-xs text-gray-400 mt-2">Supported formats: MP4, AVI, MOV, MKV (Max: 2GB)</p>
                                    </div>

                                    <!-- Selected File Info -->
                                    <div id="fileInfo" class="hidden">
                                        <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span id="fileName" class="text-sm font-medium"></span>
                                            <span id="fileSize" class="text-sm ml-2"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Button -->
                                <div class="mt-4 text-center">
                                    <button id="uploadBtn" disabled
                                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                </path>
                                            </svg>
                                            <span id="uploadBtnText">Start Upload</span>
                                        </span>
                                    </button>
                                </div>

                                <!-- Progress Section -->
                                <div id="progressSection" class="hidden mt-4">
                                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                                        <!-- Progress Header -->
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center">
                                                <div id="progressIcon" class="mr-3">
                                                    <!-- Dynamic icon will be inserted here -->
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-900">Uploading Video</h4>
                                                    <p id="progressStatus" class="text-xs text-gray-500">Initializing...</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span id="progressPercent" class="text-lg font-bold text-blue-600">0%</span>
                                                <p class="text-xs text-gray-500">Complete</p>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="relative">
                                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div id="progressBar"
                                                    class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-300 ease-out"
                                                    style="width: 0%"></div>
                                            </div>
                                            <!-- Progress Details -->
                                            <div class="flex justify-between mt-2 text-xs text-gray-500">
                                                <span id="bytesUploaded">0 MB</span>
                                                <span id="bytesTotal">0 MB</span>
                                            </div>
                                        </div>

                                        <!-- Upload Speed -->
                                        <div class="mt-3 flex items-center justify-between">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                                <span id="uploadSpeed">0 MB/s</span>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <span id="timeRemaining">Calculating...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Success/Error Messages -->
                                <div id="uploadMessage" class="hidden mt-4">
                                    <!-- Dynamic message will be inserted here -->
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
<script src="https://cdn.jsdelivr.net/npm/tus-js-client@3/dist/tus.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // TUS Upload Implementation
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadBtnText = document.getElementById('uploadBtnText');
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const progressSection = document.getElementById('progressSection');
    const progressIcon = document.getElementById('progressIcon');
    const progressStatus = document.getElementById('progressStatus');
    const progressPercent = document.getElementById('progressPercent');
    const progressBar = document.getElementById('progressBar');
    const bytesUploaded = document.getElementById('bytesUploaded');
    const bytesTotal = document.getElementById('bytesTotal');
    const uploadSpeed = document.getElementById('uploadSpeed');
    const timeRemaining = document.getElementById('timeRemaining');
    const uploadMessage = document.getElementById('uploadMessage');
    const fileInputField = document.getElementById('file');
    
    const TUS_ENDPOINT = "{{ config('app.url') }}/api/upload";
    let upload = null;
    let startTime = null;
    let lastBytesUploaded = 0;
    let speedUpdateInterval = null;

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Format time remaining
    function formatTimeRemaining(seconds) {
        if (!seconds || seconds === Infinity) return 'Calculating...';
        if (seconds < 60) return Math.round(seconds) + 's';
        if (seconds < 3600) return Math.round(seconds / 60) + 'm';
        return Math.round(seconds / 3600) + 'h';
    }

    // Show upload message
    function showMessage(message, type = 'info') {
        uploadMessage.classList.remove('hidden');
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 
                       type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 
                       'bg-blue-50 border-blue-200 text-blue-800';
        const icon = type === 'success' ? 
                   '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' :
                   type === 'error' ? 
                   '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                   '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        
        uploadMessage.innerHTML = `
            <div class="flex items-center p-4 border rounded-lg ${bgColor}">
                <div class="mr-3">${icon}</div>
                <div>
                    <p class="text-sm font-medium">${message}</p>
                </div>
            </div>
        `;
    }

    // Update progress icon
    function updateProgressIcon(type) {
        const icons = {
            uploading: '<svg class="animate-spin h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>',
            success: '<svg class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
            error: '<svg class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
        };
        progressIcon.innerHTML = icons[type] || icons.uploading;
    }

    // Calculate upload speed and time remaining
    function updateSpeedStats(currentBytes, totalBytes) {
        if (!startTime) return;
        
        const currentTime = Date.now();
        const elapsedTime = (currentTime - startTime) / 1000; // in seconds
        const bytesPerSecond = currentBytes / elapsedTime;
        const remainingBytes = totalBytes - currentBytes;
        const remainingTime = remainingBytes / bytesPerSecond;
        
        uploadSpeed.textContent = formatFileSize(bytesPerSecond) + '/s';
        timeRemaining.textContent = formatTimeRemaining(remainingTime);
    }

    // Handle file selection
    function handleFileSelect(file) {
        if (!file) return;
        
        // Validate file type
        if (!file.type.startsWith('video/')) {
            showMessage('Please select a valid video file', 'error');
            return;
        }
        
        // Validate file size (2GB limit)
        const maxSize = 2 * 1024 * 1024 * 1024;
        if (file.size > maxSize) {
            showMessage('File size must be less than 2GB', 'error');
            return;
        }
        
        // Show file info
        fileName.textContent = file.name;
        fileSize.textContent = '(' + formatFileSize(file.size) + ')';
        fileInfo.classList.remove('hidden');
        uploadBtn.disabled = false;
        uploadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        
        // Hide previous messages
        uploadMessage.classList.add('hidden');
    }

    // File input change event
    fileInput.addEventListener('change', (e) => {
        handleFileSelect(e.target.files[0]);
    });

    // Drag and drop events
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('border-blue-500', 'bg-blue-100');
    });

    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-500', 'bg-blue-100');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('border-blue-500', 'bg-blue-100');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    // Click to upload
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });

    // Upload button click event
    uploadBtn.addEventListener('click', () => {
        const file = fileInput.files[0];
        if (!file) {
            showMessage('Please select a file first', 'error');
            return;
        }

        // Reset states
        startTime = Date.now();
        lastBytesUploaded = 0;
        progressSection.classList.remove('hidden');
        uploadMessage.classList.add('hidden');
        uploadBtn.disabled = true;
        uploadBtnText.textContent = 'Uploading...';
        updateProgressIcon('uploading');
        progressStatus.textContent = 'Initializing upload...';

        // Start speed update interval
        speedUpdateInterval = setInterval(() => {
            if (upload && upload._progress) {
                updateSpeedStats(upload._progress.bytesUploaded, upload._progress.bytesTotal);
            }
        }, 1000);

        // Create TUS upload
        upload = new tus.Upload(file, {
            endpoint: TUS_ENDPOINT,
            chunkSize: 1 * 1024 * 1024, // 1MB per chunk
            metadata: {
                filename: file.name,
                filetype: file.type,
                episode_id: '{{ $episode->id }}'
            },
            onError: function (error) {
                console.error("Upload failed:", error);
                clearInterval(speedUpdateInterval);
                updateProgressIcon('error');
                progressStatus.textContent = 'Upload failed';
                uploadBtnText.textContent = 'Try Again';
                uploadBtn.disabled = false;
                showMessage('Upload failed: ' + error.message, 'error');
            },
            onProgress: function (bytesUploaded, bytesTotal) {
                const percentage = ((bytesUploaded / bytesTotal) * 100).toFixed(1);
                
                // Update progress bar
                progressBar.style.width = percentage + '%';
                progressPercent.textContent = percentage + '%';
                
                // Update file sizes
                bytesUploaded.textContent = formatFileSize(bytesUploaded);
                bytesTotal.textContent = formatFileSize(bytesTotal);
                
                // Update status
                progressStatus.textContent = 'Uploading...';
                
                // Store progress for speed calculation
                upload._progress = { bytesUploaded, bytesTotal };
            },
            onSuccess: function () {
                clearInterval(speedUpdateInterval);
                updateProgressIcon('success');
                progressStatus.textContent = 'Upload completed successfully';
                progressPercent.textContent = '100%';
                progressBar.style.width = '100%';
                uploadBtnText.textContent = 'Upload Complete';
                uploadSpeed.textContent = '0 MB/s';
                timeRemaining.textContent = 'Completed';
                showMessage('Episode video uploaded successfully! The file will be processed for streaming.', 'success');
                console.log("File available at:", upload.url);
            }
        });

        // Start upload
        upload.start();
    });

    // Download Links Management
    let downloadLinkIndex = 0;
    const downloadLinksList = document.getElementById('downloadLinksList');
    const addDownloadLinkBtn = document.getElementById('addDownloadLinkBtn');
    
    // Load existing download links
    const existingDownloadLinks = @json($episode->downloadLinks ?? []);

    function addDownloadLink(linkData = null) {
        const index = downloadLinkIndex++;
        const linkHtml = `
            <div class="download-link-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-index="${index}">
                <div class="flex justify-between items-start mb-3">
                    <h5 class="text-sm font-medium text-gray-700">Download Link #${index + 1}</h5>
                    <button type="button" onclick="removeDownloadLink(${index})" 
                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">URL *</label>
                        <input type="url" name="download_links[${index}][url]" 
                            value="${linkData ? linkData.url : ''}"
                            placeholder="https://example.com/video.mp4"
                            required
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quality *</label>
                        <select name="download_links[${index}][quality]" 
                            required
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Select Quality</option>
                            <option value="540p" ${linkData && linkData.quality === '540p' ? 'selected' : ''}>540p</option>
                            <option value="720p" ${linkData && linkData.quality === '720p' ? 'selected' : ''}>720p</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Label (Optional)</label>
                        <input type="text" name="download_links[${index}][label]" 
                            value="${linkData ? linkData.label : ''}"
                            placeholder="e.g., Direct Download"
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="hidden" name="download_links[${index}][is_active]" value="0">
                        <input type="checkbox" name="download_links[${index}][is_active]" 
                            ${linkData ? (linkData.is_active ? 'checked' : '') : 'checked'}
                            value="1"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-xs text-gray-700">Active</span>
                    </label>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="download_links[${index}][sort_order]" 
                            value="${linkData ? linkData.sort_order : index}"
                            min="0"
                            class="w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        `;
        
        downloadLinksList.insertAdjacentHTML('beforeend', linkHtml);
    }

    function removeDownloadLink(index) {
        const element = document.querySelector(`.download-link-item[data-index="${index}"]`);
        if (element) {
            element.remove();
        }
    }

    addDownloadLinkBtn.addEventListener('click', function() {
        addDownloadLink();
    });

    // Form submission cleanup - remove empty download links
    document.querySelector('form').addEventListener('submit', function(e) {
        const downloadLinkItems = document.querySelectorAll('.download-link-item');
        downloadLinkItems.forEach(function(item) {
            const urlInput = item.querySelector('input[name$="[url]"]');
            const qualitySelect = item.querySelector('select[name$="[quality]"]');
            
            // Remove the entire download link item if URL is empty
            if (urlInput && !urlInput.value.trim()) {
                item.remove();
            }
        });
    });

    // Load existing download links
    if (existingDownloadLinks && existingDownloadLinks.length > 0) {
        existingDownloadLinks.forEach(function(link) {
            addDownloadLink(link);
        });
    } else {
        // Add one empty download link if no existing ones
        addDownloadLink();
    }
});
</script>
@endpush
@endsection

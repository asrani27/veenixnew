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

                        <!-- Episode Video Upload Process Table -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Episode Video Upload Process</label>
                            <input type="hidden" name="file" id="file" value="{{ old('file', $episode->file) }}">
                            
                            <!-- Upload Process Table -->
                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900">Video Upload Process</h4>
                                    <p class="text-xs text-gray-600 mt-1">Complete each step to make your episode ready for streaming.</p>
                                </div>

                                <div class="p-4">
                                    <!-- Process Table -->
                                    <div class="overflow-x-auto">
                                        <table class="w-full border-collapse">
                                            <thead>
                                                <tr class="border-b border-gray-200">
                                                    <th class="text-left py-2 px-3 font-medium text-gray-700 w-8 text-xs">No</th>
                                                    <th class="text-left py-2 px-3 font-medium text-gray-700 text-xs">Keterangan</th>
                                                    <th class="text-left py-2 px-3 font-medium text-gray-700 text-xs">Status</th>
                                                    <th class="text-center py-2 px-3 font-medium text-gray-700 text-xs">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Step 1: Upload File -->
                                                <tr class="border-b border-gray-100" id="uploadStep">
                                                    <td class="py-3 px-3">
                                                        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 font-medium text-xs">
                                                            1
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-3">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                            </svg>
                                                            <div>
                                                                <p class="font-medium text-gray-900 text-sm">Proses Upload</p>
                                                                <p class="text-xs text-gray-500">Upload file video ke server lokal menggunakan TUS protocol</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-3">
                                                        <div id="uploadStatus" class="flex items-center">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <circle cx="10" cy="10" r="6" fill="currentColor" opacity="0.3"></circle>
                                                                </svg>
                                                                Menunggu File
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-3 text-center">
                                                        <div id="uploadAction">
                                                            <input type="file" id="fileInput" class="hidden" accept="video/*" />
                                                            <button id="uploadBtn" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                                                <span class="flex items-center">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                                    </svg>
                                                                    <span id="uploadBtnText">Pilih File</span>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Step 2: Convert to HLS -->
                                                <tr class="border-b border-gray-100" id="convertStep">
                                                    <td class="py-3 px-3">
                                                        <div class="flex items-center justify-center w-6 h-6 rounded-full bg-yellow-100 text-yellow-600 font-medium text-xs">
                                                            2
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-3">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                            </svg>
                                                            <div>
                                                                <p class="font-medium text-gray-900 text-sm">Convert To HLS</p>
                                                                <p class="text-xs text-gray-500">Konversi video ke format HLS untuk streaming</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-3">
                                                        <div id="convertStatus" class="flex items-center">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <circle cx="10" cy="10" r="6" fill="currentColor" opacity="0.3"></circle>
                                                                </svg>
                                                                Menunggu Upload
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-3 text-center">
                                                        <div id="convertAction">
                                                            <button id="convertHlsBtn" disabled class="px-3 py-1.5 bg-yellow-600 text-white text-xs font-medium rounded hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                                                <span class="flex items-center">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                    </svg>
                                                                    <span id="convertBtnText">Convert</span>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- File Upload Area (Hidden by default) -->
                                    <div id="uploadArea" class="hidden mt-4">
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200 bg-gray-50 hover:bg-blue-50 cursor-pointer">
                                            <input type="file" id="fileInputHidden" class="hidden" accept="video/*" />

                                            <!-- Upload Icon -->
                                            <div class="mb-4">
                                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                            </div>

                                            <!-- Upload Text -->
                                            <div class="mb-4">
                                                <p class="text-sm font-medium text-gray-900 mb-2">Drag & drop video file here</p>
                                                <p class="text-xs text-gray-500">or click to browse</p>
                                                <p class="text-xs text-gray-400 mt-1">Supported formats: MP4, AVI, MOV, MKV (Max: 2GB)</p>
                                            </div>

                                            <!-- Selected File Info -->
                                            <div id="fileInfo" class="hidden">
                                                <div class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-800 rounded-lg">
                                                    <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span id="fileName" class="text-xs font-medium"></span>
                                                    <span id="fileSize" class="text-xs ml-2"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progress Sections -->
                                    <div id="uploadProgress" class="hidden mt-4">
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-medium text-blue-800">Uploading Video</span>
                                                <span id="uploadProgressPercent" class="text-xs font-medium text-blue-800">0%</span>
                                            </div>
                                            <div class="w-full bg-blue-200 rounded-full h-1.5">
                                                <div id="uploadProgressBar" class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                            </div>
                                            <p id="uploadProgressStatus" class="text-xs text-blue-600 mt-1">Initializing upload...</p>
                                        </div>
                                    </div>

                                    <div id="convertProgress" class="hidden mt-4">
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-medium text-yellow-800">Converting to HLS</span>
                                                <span id="convertProgressPercent" class="text-xs font-medium text-yellow-800">0%</span>
                                            </div>
                                            <div class="w-full bg-yellow-200 rounded-full h-1.5">
                                                <div id="convertProgressBar" class="bg-yellow-600 h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                            </div>
                                            <p id="convertProgressStatus" class="text-xs text-yellow-600 mt-1">Starting conversion...</p>
                                        </div>
                                    </div>

                                    <!-- Messages -->
                                    <div id="messageArea" class="mt-4">
                                        <!-- Dynamic messages will be inserted here -->
                                    </div>
                                </div>
                            </div>
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
    // Table-based Upload Implementation
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadBtnText = document.getElementById('uploadBtnText');
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.getElementById('uploadArea');
    const fileInputHidden = document.getElementById('fileInputHidden');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadProgress = document.getElementById('uploadProgress');
    const uploadProgressBar = document.getElementById('uploadProgressBar');
    const uploadProgressPercent = document.getElementById('uploadProgressPercent');
    const uploadProgressStatus = document.getElementById('uploadProgressStatus');
    const convertProgress = document.getElementById('convertProgress');
    const convertProgressBar = document.getElementById('convertProgressBar');
    const convertProgressPercent = document.getElementById('convertProgressPercent');
    const convertProgressStatus = document.getElementById('convertProgressStatus');
    const messageArea = document.getElementById('messageArea');
    const convertHlsBtn = document.getElementById('convertHlsBtn');
    const convertBtnText = document.getElementById('convertBtnText');
    const fileInputField = document.getElementById('file');
    
    const TUS_ENDPOINT = "{{ config('app.url') }}/api/upload";
    const CONVERT_ENDPOINT = "{{ route('admin.tv.episode.convert-hls', $episode->id) }}";
    const PROGRESS_ENDPOINT = "{{ route('admin.tv.episode.hls-progress', $episode->id) }}";
    
    let upload = null;
    let conversionInterval = null;
    let uploadedFileUrl = null;

    // Status update functions for table
    function updateStepStatus(step, status, details = '') {
        const statusElement = document.getElementById(step + 'Status');
        const actionElement = document.getElementById(step + 'Action');
        
        let statusHtml = '';
        let isDisabled = true;
        
        switch(status) {
            case 'waiting':
                statusHtml = `
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="6" fill="currentColor" opacity="0.3"></circle>
                        </svg>
                        ${details || 'Menunggu'}
                    </span>
                `;
                break;
            case 'ready':
                statusHtml = `
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        ${details || 'Ready'}
                    </span>
                `;
                isDisabled = false;
                break;
            case 'processing':
                statusHtml = `
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <svg class="animate-spin w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="6" fill="currentColor" opacity="0.3"></circle>
                        </svg>
                        ${details || 'Processing'}
                    </span>
                `;
                isDisabled = true;
                break;
            case 'completed':
                statusHtml = `
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        ${details || 'Completed'}
                    </span>
                `;
                isDisabled = true;
                break;
            case 'error':
                statusHtml = `
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        ${details || 'Error'}
                    </span>
                `;
                isDisabled = false;
                break;
        }
        
        statusElement.innerHTML = statusHtml;
        
        // Update button state
        const button = actionElement.querySelector('button');
        if (button) {
            button.disabled = isDisabled;
            if (isDisabled) {
                button.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    // Show message
    function showMessage(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 
                       type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 
                       'bg-blue-50 border-blue-200 text-blue-800';
        const icon = type === 'success' ? 
                   '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' :
                   type === 'error' ? 
                   '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                   '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        
        messageArea.innerHTML = `
            <div class="flex items-center p-4 border rounded-lg ${bgColor}">
                <div class="mr-3">${icon}</div>
                <div>
                    <p class="text-sm font-medium">${message}</p>
                </div>
            </div>
        `;
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
        uploadBtnText.textContent = 'Upload';
        
        // Update status
        updateStepStatus('upload', 'ready', 'File ready');
    }

    // Upload button click event
    uploadBtn.addEventListener('click', () => {
        if (uploadBtnText.textContent === 'Pilih File') {
            fileInput.click();
        } else if (uploadBtnText.textContent === 'Upload') {
            startUpload();
        }
    });

    // File input change event
    fileInput.addEventListener('change', (e) => {
        handleFileSelect(e.target.files[0]);
    });

    // Start upload function
    function startUpload() {
        const file = fileInput.files[0];
        if (!file) {
            showMessage('Please select a file first', 'error');
            return;
        }

        // Show upload progress
        uploadProgress.classList.remove('hidden');
        updateStepStatus('upload', 'processing', 'Uploading...');
        uploadBtnText.textContent = 'Uploading...';

        // Create TUS upload
        upload = new tus.Upload(file, {
            endpoint: TUS_ENDPOINT,
            chunkSize: 1 * 1024 * 1024,
            metadata: {
                filename: file.name,
                filetype: file.type,
                episode_id: '{{ $episode->id }}'
            },
            onError: function (error) {
                console.error("Upload failed:", error);
                updateStepStatus('upload', 'error', 'Upload failed');
                uploadBtnText.textContent = 'Try Again';
                showMessage('Upload failed: ' + error.message, 'error');
            },
            onProgress: function (bytesUploaded, bytesTotal) {
                const percentage = ((bytesUploaded / bytesTotal) * 100).toFixed(1);
                uploadProgressBar.style.width = percentage + '%';
                uploadProgressPercent.textContent = percentage + '%';
                uploadProgressStatus.textContent = 'Uploading...';
            },
            onSuccess: function () {
                // Use the correct file URL based on the uploaded file
                const filename = file.name;
                uploadedFileUrl = "{{ config('app.url') }}/storage/uploads/" + filename;
                
                updateStepStatus('upload', 'completed', 'Upload Complete');
                uploadProgressPercent.textContent = '100%';
                uploadProgressBar.style.width = '100%';
                uploadProgressStatus.textContent = 'Upload completed successfully';
                uploadBtnText.textContent = 'Pilih File';
                
                // Manually enable upload button for new uploads
                uploadBtn.disabled = false;
                uploadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                
                // Update hidden file input
                fileInputField.value = filename;
                
                // Save the filename to database via AJAX
                saveFilenameToDatabase(filename);
                
                // Enable next step
                updateStepStatus('convert', 'ready', 'Ready to convert');
                showMessage('Video uploaded successfully! Click "Convert" to process the video for streaming.', 'success');
                
                console.log('Upload completed. File URL:', uploadedFileUrl);
            }
        });

        upload.start();
    }

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

    // Save filename to database after upload
    async function saveFilenameToDatabase(filename) {
        try {
            const response = await fetch('{{ route("admin.tv.episode.update", ["tv" => $tv->id, "season" => $season->season_number, "episode" => $episode->episode_number]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: new URLSearchParams({
                    '_method': 'PUT',
                    'file': filename,
                    // Include other form data that might be needed
                    'name': document.querySelector('input[name="name"]')?.value || '',
                    'overview': document.querySelector('textarea[name="overview"]')?.value || '',
                    'air_date': document.querySelector('input[name="air_date"]')?.value || '',
                    'runtime': document.querySelector('input[name="runtime"]')?.value || '',
                    'vote_average': document.querySelector('input[name="vote_average"]')?.value || '',
                    'vote_count': document.querySelector('input[name="vote_count"]')?.value || ''
                })
            });
            
            if (!response.ok) {
                console.error('Failed to save filename to database');
                return false;
            }
            
            const result = await response.json();
            console.log('Filename saved to database successfully');
            return true;
        } catch (error) {
            console.error('Error saving filename to database:', error);
            return false;
        }
    }

    // Convert HLS button click event
    convertHlsBtn.addEventListener('click', async () => {
        if (convertHlsBtn.disabled) return;
        
        try {
            convertProgress.classList.remove('hidden');
            updateStepStatus('convert', 'processing', 'Converting...');
            convertBtnText.textContent = 'Converting...';
            
            const response = await fetch(CONVERT_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    file_url: uploadedFileUrl
                })
            });
            
            if (!response.ok) {
                throw new Error('Conversion request failed');
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Start polling for progress
                pollConversionProgress();
            } else {
                throw new Error(result.message || 'Conversion failed to start');
            }
            
        } catch (error) {
            console.error('Conversion error:', error);
            updateStepStatus('convert', 'error', 'Conversion failed');
            convertBtnText.textContent = 'Try Again';
            showMessage('Conversion failed: ' + error.message, 'error');
        }
    });

    // Poll conversion progress
    function pollConversionProgress() {
        conversionInterval = setInterval(async () => {
            try {
                const response = await fetch(PROGRESS_ENDPOINT);
                if (!response.ok) return;
                
                const data = await response.json();
                
                if (data.status === 'processing') {
                    convertProgressBar.style.width = data.progress + '%';
                    convertProgressPercent.textContent = data.progress + '%';
                    convertProgressStatus.textContent = data.message || 'Processing...';
                    
                } else if (data.status === 'completed') {
                    clearInterval(conversionInterval);
                    updateStepStatus('convert', 'completed', 'HLS Ready');
                    convertProgressPercent.textContent = '100%';
                    convertProgressBar.style.width = '100%';
                    convertProgressStatus.textContent = 'Conversion completed successfully';
                    convertBtnText.textContent = 'Converted';
                    
                    showMessage('Video converted successfully! Your video is now ready for streaming.', 'success');
                    
                } else if (data.status === 'failed') {
                    clearInterval(conversionInterval);
                    updateStepStatus('convert', 'error', 'Conversion failed');
                    convertBtnText.textContent = 'Try Again';
                    showMessage('Conversion failed: ' + (data.error || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error polling conversion progress:', error);
            }
        }, 2000);
    }
    
    // Update HLS status UI
    function updateHlsStatus(status, message, progress = null) {
        // Update status icon
        const iconHtml = {
            processing: '<div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center"><svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>',
            completed: '<div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center"><svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></div>',
            failed: '<div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center"><svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg></div>'
        };
        
        if (hlsStatusIcon && iconHtml[status]) {
            hlsStatusIcon.innerHTML = iconHtml[status];
        }
        
        // Update status message
        if (hlsStatusMessage) {
            hlsStatusMessage.textContent = message;
        }
        
        // Update status title
        const statusTitle = hlsStatusIcon?.closest('.bg-gray-50')?.querySelector('h5');
        if (statusTitle) {
            const titles = {
                processing: 'Converting to HLS...',
                completed: 'Conversion Complete',
                failed: 'Conversion Failed'
            };
            statusTitle.textContent = titles[status] || 'Unknown Status';
        }
    }
    
    // Monitor conversion progress
    let progressInterval = null;
    
    function startProgressMonitoring(episodeId) {
        // Clear any existing interval
        if (progressInterval) {
            clearInterval(progressInterval);
        }
        
        // Start polling for progress
        progressInterval = setInterval(() => {
            fetch(`/admin/tv/episode/${episodeId}/hls-progress`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'completed') {
                        updateHlsStatus('completed', 'Episode is ready for HLS streaming');
                        clearInterval(progressInterval);
                        
                        // Reload page after a short delay to show updated UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else if (data.status === 'failed') {
                        updateHlsStatus('failed', data.error_message || 'Conversion failed. Please try again.');
                        clearInterval(progressInterval);
                        
                        // Restore convert button
                        if (convertHlsBtn) {
                            convertHlsBtn.disabled = false;
                            convertHlsBtn.innerHTML = `
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Retry Conversion
                            `;
                        }
                    } else if (data.status === 'processing') {
                        updateHlsStatus('processing', data.message || 'Processing video file...', data.progress);
                        
                        // Update progress bar if it exists
                        const progressBar = document.querySelector('.bg-gradient-to-r.from-blue-500');
                        const progressPercent = document.querySelector('.text-blue-600');
                        if (progressBar && data.progress) {
                            progressBar.style.width = data.progress + '%';
                        }
                        if (progressPercent && data.progress) {
                            progressPercent.textContent = data.progress + '%';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking HLS progress:', error);
                });
        }, 3000); // Check every 3 seconds
    }
    
    // Check existing episode status and initialize interface
    function initializeEpisodeStatus() {
        const episodeData = @json($episode->toArray());
        
        console.log('Episode data:', episodeData); // Debug log
        
        // Check if file exists (upload step)
        if (episodeData.file && episodeData.file !== null && episodeData.file !== '') {
            console.log('File exists:', episodeData.file); // Debug log
            
            // File exists - show completed status but keep button enabled
            updateStepStatus('upload', 'completed', 'Upload Complete');
            uploadBtnText.textContent = 'Pilih File';
            uploadedFileUrl = "{{ config('app.url') }}/storage/uploads/" + episodeData.file;
            
            // Manually enable upload button for new uploads
            uploadBtn.disabled = false;
            uploadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            
            // Check HLS status
            const hlsStatus = episodeData.hls_status || 'pending';
            const hlsProgress = episodeData.hls_progress || 0;
            
            if (hlsStatus === 'completed') {
                updateStepStatus('convert', 'completed', 'HLS Ready');
                convertBtnText.textContent = 'Converted';
            } else if (hlsStatus === 'processing') {
                updateStepStatus('convert', 'processing', 'Converting...');
                convertBtnText.textContent = 'Converting...';
                convertProgress.classList.remove('hidden');
                convertProgressBar.style.width = hlsProgress + '%';
                convertProgressPercent.textContent = hlsProgress + '%';
                convertProgressStatus.textContent = 'Converting to HLS...';
                
                // Start polling for progress if processing
                pollConversionProgress();
            } else if (hlsStatus === 'failed') {
                updateStepStatus('convert', 'error', 'Conversion failed');
                convertBtnText.textContent = 'Try Again';
            } else {
                updateStepStatus('convert', 'ready', 'Ready to convert');
            }
        } else {
            console.log('No file exists'); // Debug log
            
            // No file uploaded yet - show waiting status but keep button enabled
            updateStepStatus('upload', 'waiting', 'Menunggu File');
            updateStepStatus('convert', 'waiting', 'Menunggu Upload');
            
            // Ensure upload button is enabled
            uploadBtn.disabled = false;
            uploadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            uploadBtnText.textContent = 'Pilih File';
        }
    }

    // Initialize on page load
    initializeEpisodeStatus();

    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', () => {
        if (progressInterval) {
            clearInterval(progressInterval);
        }
        if (conversionInterval) {
            clearInterval(conversionInterval);
        }
    });
});
</script>
@endpush
@endsection

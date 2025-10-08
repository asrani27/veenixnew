@extends('layouts.app')

@section('title', 'Upload Video - ' . $movie->title)

@section('header_title', 'Upload Video for ' . $movie->title)

@section('content')
<div class="container mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.movies.index') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                        </path>
                    </svg>
                    Movie Management
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('admin.movies.edit', $movie->id) }}"
                        class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                        {{ $movie->title }}
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Upload Video</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Movie Info Card -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200 mb-6">
        <div class="p-6">
            <div class="flex items-start space-x-4">
                <img src="{{ $movie->poster_url ?? asset('images/sample.jpg') }}" alt="{{ $movie->title }}"
                    class="w-20 h-28 rounded-lg object-cover shadow-md">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $movie->title }}</h2>
                    @if($movie->original_title && $movie->original_title !== $movie->title)
                    <p class="text-gray-600 mb-2">Original Title: {{ $movie->original_title }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                        @if($movie->release_date)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ date('Y', strtotime($movie->release_date)) }}
                        </span>
                        @endif
                        @if($movie->runtime)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $movie->runtime }} min
                        </span>
                        @endif
                    </div>
                    @if($movie->overview)
                    <p class="text-gray-700 mt-3 line-clamp-2">{{ Str::limit($movie->overview, 200) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Upload Video File (TUS Resumable Upload)</h3>
            <p class="text-sm text-gray-600 mt-1">Upload a video file for this movie using TUS protocol for resumable
                uploads. The file will be converted to HLS format for streaming.</p>
        </div>

        <div class="p-6">
            <!-- Current Video Status -->
            @if($movie->file)
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800">Video already uploaded</p>
                        <p class="text-xs text-green-600">Uploading a new video will replace the existing one.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Upload Form -->
            <div class="max-w-2xl mx-auto">
                <!-- File Upload Area -->
                <div id="uploadArea"
                    class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors duration-200 bg-gray-50 hover:bg-blue-50 cursor-pointer">
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
                <div class="mt-6 text-center">
                    <button id="uploadBtn" disabled
                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                            <span id="uploadBtnText">Mulai Upload</span>
                        </span>
                    </button>
                </div>

                <!-- Progress Section -->
                <div id="progressSection" class="hidden mt-8">
                    <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                        <!-- Progress Header -->
                        <div class="flex items-center justify-between mb-4">
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
                                <span id="progressPercent" class="text-2xl font-bold text-blue-600">0%</span>
                                <p class="text-xs text-gray-500">Complete</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="relative">
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div id="progressBar"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-300 ease-out"
                                    style="width: 0%"></div>
                            </div>
                            <!-- Progress Details -->
                            <div class="flex justify-between mt-2 text-xs text-gray-500">
                                <span id="bytesUploaded">0 MB</span>
                                <span id="bytesTotal">0 MB</span>
                            </div>
                        </div>

                        <!-- Upload Speed -->
                        <div class="mt-4 flex items-center justify-between">
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
                <div id="uploadMessage" class="hidden mt-6">
                    <!-- Dynamic message will be inserted here -->
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/tus-js-client@3/dist/tus.min.js"></script>
<script>
    // DOM Elements
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
            chunkSize: 1 * 1024 * 1024, // 5MB per chunk
            metadata: {
                filename: file.name,
                filetype: file.type,
                movie_id: '{{ $movie->id }}'
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
                showMessage('Video uploaded successfully! The file will be processed for streaming.', 'success');
                console.log("File available at:", upload.url);
            }
        });

        // Start upload
        upload.start();
    });
</script>
@endsection
@extends('layouts.app')

@section('title', 'Upload Video - ' . ($movie->title ?? 'Unknown Movie'))

@section('header_title', 'Upload Video for ' . ($movie->title ?? 'Unknown Movie'))

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
                    @if($movie && $movie->id)
                    <a href="{{ route('admin.movies.edit', $movie->id) }}"
                        class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                        {{ $movie->title }}
                    </a>
                    @else
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                        {{ $movie->title ?? 'Unknown Movie' }}
                    </span>
                    @endif
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

    <!-- Upload Process Table -->
    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Video Upload Process</h3>
            <p class="text-sm text-gray-600 mt-1">Complete each step to make your video ready for streaming.</p>
        </div>

        <div class="p-6">
            <!-- Process Table -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-medium text-gray-700 w-12">No</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Keterangan</th>
                            <th class="text-center py-3 px-4 font-medium text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Step 1: Upload File -->
                        <tr class="border-b border-gray-100" id="uploadStep">
                            <td class="py-4 px-4">
                                <div
                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-medium">
                                    1
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                        </path>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-gray-900">Proses Upload</p>
                                        <p class="text-sm text-gray-500">Upload file video ke server lokal menggunakan
                                            TUS protocol</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div id="uploadAction">
                                    <input type="file" id="fileInput" class="hidden" accept="video/*" />
                                    <button id="uploadBtn"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                </path>
                                            </svg>
                                            <span id="uploadBtnText">Pilih File</span>
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </div>

            <!-- File Upload Area (Hidden by default) -->
            <div id="uploadArea" class="hidden mt-6">
                <div
                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors duration-200 bg-gray-50 hover:bg-blue-50 cursor-pointer">
                    <input type="file" id="fileInputHidden" class="hidden" accept="video/*" />

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
            </div>

            <!-- Status Progress Section -->
            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Status Progress</h4>
                
                <!-- Progress 1: Upload -->
                <div class="mb-4">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-medium mr-3">1</div>
                                <span class="text-sm font-medium text-gray-900">Progress Upload</span>
                            </div>
                            <span id="uploadProgressPercent" class="text-sm font-medium text-gray-600">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="uploadProgressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="uploadProgressStatus" class="text-xs text-gray-500 mt-2">Menunggu file...</p>
                    </div>
                </div>

            </div>



            <!-- Messages -->
            <div id="messageArea" class="mt-6">
                <!-- Dynamic messages will be inserted here -->
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/tus-js-client@3/dist/tus.min.js"></script>
<script>
    // DOM Elements
    const fileInput = document.getElementById('fileInput');
    const fileInputHidden = document.getElementById('fileInputHidden');
    const uploadArea = document.getElementById('uploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadBtnText = document.getElementById('uploadBtnText');
    const messageArea = document.getElementById('messageArea');
    
    const TUS_ENDPOINT = "{{ config('app.url') }}/api/upload";
    let upload = null;
    let uploadedFileUrl = null;

    // Progress update functions
    function updateProgress(progressType, percentage, status) {
        const progressBar = document.getElementById(progressType + 'ProgressBar');
        const progressPercent = document.getElementById(progressType + 'ProgressPercent');
        const progressStatus = document.getElementById(progressType + 'ProgressStatus');
        
        if (progressBar && progressPercent && progressStatus) {
            progressBar.style.width = percentage + '%';
            progressPercent.textContent = percentage + '%';
            progressStatus.textContent = status;
        }
        
        // Only handle upload progress type
        if (progressType === 'upload') {
            // Determine status value
            let statusValue = 'waiting';
            if (percentage > 0 && percentage < 100) {
                statusValue = 'processing';
            } else if (percentage === 100) {
                statusValue = 'completed';
            } else if (status.toLowerCase().includes('error') || status.toLowerCase().includes('gagal')) {
                statusValue = 'error';
            } else if (status.toLowerCase().includes('ready') || status.toLowerCase().includes('siap')) {
                statusValue = 'ready';
            }
            
            // Update database
            updateMovieStatus('status_upload_to_local', statusValue, Math.round(percentage));
        }
    }

    // Show file URL
    function showFileUrl(fileUrl) {
        // Add file URL display to message area
        const urlDisplay = document.createElement('div');
        urlDisplay.className = 'mt-3 text-center';
        urlDisplay.innerHTML = `
            <a href="${fileUrl}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 underline inline-flex items-center px-3 py-1 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                View File
            </a>
        `;
        
        // Add to message area
        messageArea.appendChild(urlDisplay);
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
        
        // Update progress
        updateProgress('upload', 0, 'File ready');
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

        // Start upload progress
        updateProgress('upload', 0, 'Starting upload...');
        uploadBtnText.textContent = 'Uploading...';

        // Create TUS upload
        upload = new tus.Upload(file, {
            endpoint: TUS_ENDPOINT,
            chunkSize: 1 * 1024 * 1024,
            metadata: {
                filename: file.name,
                filetype: file.type,
                movie_id: '{{ $movie->id }}'
            },
            onError: function (error) {
                console.error("Upload failed:", error);
                updateProgress('upload', 0, 'Upload failed');
                uploadBtnText.textContent = 'Try Again';
                showMessage('Upload failed: ' + error.message, 'error');
            },
            onProgress: function (bytesUploaded, bytesTotal) {
                const percentage = ((bytesUploaded / bytesTotal) * 100).toFixed(1);
                updateProgress('upload', percentage, 'Uploading...');
            },
            onSuccess: function () {
                // Use the correct file URL based on the uploaded file
                const filename = file.name;
                uploadedFileUrl = "{{ config('app.url') }}/storage/uploads/" + filename;
                
                updateProgress('upload', 100, 'Upload completed successfully');
                uploadBtnText.textContent = 'Pilih File';
                
                // Manually enable upload button for new uploads
                uploadBtn.disabled = false;
                uploadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                
                // Show file URL
                showFileUrl(uploadedFileUrl);
                
                showMessage('Video uploaded successfully!', 'success');
                
                console.log('Upload completed. File URL:', uploadedFileUrl); // Debug log
            }
        });

        upload.start();
    }



    // Check existing movie status and initialize interface
    function initializeMovieStatus() {
        const movieData = @json($movie->toArray());
        
        console.log('Movie data:', movieData); // Debug log
        
        // Check upload status - if completed, set progress to 100%
        let uploadProgress = movieData.progress_upload_to_local || 0;
        let uploadStatus = movieData.status_upload_to_local || 'waiting';
        
        if (uploadStatus === 'completed') {
            uploadProgress = 100;
        }
        
        // Initialize progress bars from database
        updateProgress('upload', uploadProgress, getStatusText(uploadStatus));
        
        // Check if file exists (upload step)
        if (movieData.file && movieData.file !== null && movieData.file !== '') {
            console.log('File exists:', movieData.file); // Debug log
            
            uploadedFileUrl = "{{ config('app.url') }}/storage/uploads/" + movieData.file;
            
            // Show existing file URL
            showFileUrl(uploadedFileUrl);
            
        } else {
            console.log('No file exists'); // Debug log
        }
        
        // Ensure upload button is enabled
        uploadBtn.disabled = false;
        uploadBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        uploadBtnText.textContent = 'Pilih File';
    }

    // Get status text in Indonesian
    function getStatusText(status) {
        const statusTexts = {
            'waiting': 'Menunggu...',
            'processing': 'Memproses...',
            'completed': 'Selesai',
            'error': 'Gagal',
            'ready': 'Siap'
        };
        return statusTexts[status] || 'Menunggu...';
    }

    // Update movie status in database
    function updateMovieStatus(field, status, progress = 0) {
        fetch("{{ route('admin.movies.update-status', $movie->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                field: field,
                status: status,
                progress: progress
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Status updated successfully');
            } else {
                console.error('Failed to update status:', data.message);
            }
        })
        .catch(error => {
            console.error('Error updating status:', error);
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeMovieStatus();
    });
</script>
@endsection

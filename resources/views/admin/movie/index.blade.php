@extends('layouts.app')

@section('title', 'Movie Management')

@section('header_title', 'Movie Management')

@section('content')
<div class="container mx-auto">
    <!-- Header with Add Movie Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Movie Management</h3>
            <p class="text-gray-600 mt-1">Manage your movie collection</p>
        </div>
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- TMDB Search Button -->
            <a href="{{ route('admin.movies.search-tmdb') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:ring-opacity-50">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Search TMDB
            </a>
            <!-- Add Movie Button -->
            {{-- <a href="{{ route('admin.movies.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Movie
            </a> --}}
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-3 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h3 class="text-lg font-medium text-gray-900">Movie List</h3>
                <!-- Search Form -->
                <form class="flex-1 sm:flex-initial sm:max-w-xs" method="GET"
                    action="{{ route('admin.movies.index') }}">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search movies..." value="{{ request('search') }}"
                            class="w-full pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg shadow-sm">
                    <thead class="bg-gray-50 border-b border-gray-300">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                No</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Image</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Title</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Views</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Total Download</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Video Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                HLS Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($data ?? [] as $index => $item)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">{{
                                $index + 1 }}</td>
                            <td class="px-6 py-2 whitespace-nowrap border-r border-gray-200">
                                <img src="{{ $item->poster_url ?? asset('images/sample.jpg') }}"
                                    alt="{{ $item->title ?? 'Movie Image' }}" class="h-12 w-12 rounded-lg object-cover">
                            </td>
                            <td
                                class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                                <div>{{ $item->title ?? 'Untitled' }}</div>
                                <div class="text-xs text-gray-500">
                                    Genre : {{ $item->genres->pluck('name')->join(', ') ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Country : {{ $item->countries->pluck('english_name')->join(', ') ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">{{
                                $item->views ?? 0 }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">{{
                                $item->downloads ?? 0 }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium border-r border-gray-200">
                                <a href="/movie/stream/{{$item->tmdb_id}}" target="_blank"
                                    class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-xs font-medium rounded-md shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Stream
                                </a>
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium border-r border-gray-200">
                                @php
                                    $hlsStatus = $item->hls_status ?? 'pending';
                                    $hlsProgress = $item->hls_progress ?? 0;
                                @endphp
                                
                                @if($hlsStatus === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        HLS Ready
                                    </span>
                                @elseif($hlsStatus === 'processing')
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Converting
                                        </span>
                                        <span class="text-xs text-gray-600 font-medium">{{ $hlsProgress }}%</span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $hlsProgress }}%"></div>
                                    </div>
                                @elseif($hlsStatus === 'failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.movies.edit', $item->id) }}"
                                        class="inline-flex items-center p-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.movies.upload', $item->id) }}"
                                        class="inline-flex items-center p-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                                        title="Upload Movie Video">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.movies.destroy', $item->id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this movie? This will also delete all associated video files from cloud storage.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center p-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if (empty($data))
                        <tr>
                            <td colspan="8"
                                class="px-6 py-8 text-center text-sm text-gray-500 border-r border-gray-200">
                                No movies found.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ $data->firstItem() }}</span>
                    to
                    <span class="font-medium">{{ $data->lastItem() }}</span>
                    of
                    <span class="font-medium">{{ $data->total() }}</span>
                    results
                </div>

                <div class="flex space-x-2">
                    @if ($data->hasPages())
                    <!-- Previous button -->
                    <a href="{{ $data->previousPageUrl() }}"
                        class="{{ $data->onFirstPage() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100' }} relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md transition-colors"
                        {{ $data->onFirstPage() ? 'disabled' : '' }}>
                        Previous
                    </a>

                    <!-- Page numbers -->
                    @foreach ($elements = $data->links()->elements as $element)
                    @if (is_string($element))
                    <span
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300">
                        {!! $element !!}
                    </span>
                    @endif

                    @if (is_array($element))
                    @foreach ($element as $page => $url)
                    @if ($page == $data->currentPage())
                    <span
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                        {{ $page }}
                    </span>
                    @else
                    <a href="{{ $url }}"
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 transition-colors">
                        {{ $page }}
                    </a>
                    @endif
                    @endforeach
                    @endif
                    @endforeach

                    <!-- Next button -->
                    <a href="{{ $data->nextPageUrl() }}"
                        class="{{ $data->hasMorePages() ? 'hover:bg-gray-100' : 'opacity-50 cursor-not-allowed' }} relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md transition-colors"
                        {{ !$data->hasMorePages() ? 'disabled' : '' }}>
                        Next
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh for HLS progress
let refreshInterval;

function startAutoRefresh() {
    // Refresh every 5 seconds when there are processing jobs
    refreshInterval = setInterval(() => {
        checkProcessingJobs();
    }, 5000);
}

function checkProcessingJobs() {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Create a temporary DOM element to parse the response
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        
        // Find all HLS status cells
        const hlsStatusCells = document.querySelectorAll('td:nth-child(7)');
        const newHlsStatusCells = tempDiv.querySelectorAll('td:nth-child(7)');
        
        let hasProcessingJobs = false;
        
        hlsStatusCells.forEach((cell, index) => {
            if (newHlsStatusCells[index]) {
                const newCell = newHlsStatusCells[index];
                
                // Check if this is a processing job
                if (newCell.innerHTML.includes('Converting') || newCell.innerHTML.includes('animate-spin')) {
                    hasProcessingJobs = true;
                    
                    // Update the cell with new content
                    cell.innerHTML = newCell.innerHTML;
                }
            }
        });
        
        // If no processing jobs found, stop auto-refresh
        if (!hasProcessingJobs) {
            clearInterval(refreshInterval);
            console.log('No processing jobs found, stopping auto-refresh');
        }
    })
    .catch(error => {
        console.error('Error checking processing jobs:', error);
    });
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check if there are any processing jobs on initial load
    const hasProcessingJobs = document.querySelector('.animate-spin');
    if (hasProcessingJobs) {
        console.log('Processing jobs detected, starting auto-refresh');
        startAutoRefresh();
    }
});

// Stop auto-refresh when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        // When page becomes visible again, check for processing jobs
        const hasProcessingJobs = document.querySelector('.animate-spin');
        if (hasProcessingJobs) {
            startAutoRefresh();
        }
    }
});

// Clean up interval when page is unloaded
window.addEventListener('beforeunload', function() {
    clearInterval(refreshInterval);
});

</script>
@endsection

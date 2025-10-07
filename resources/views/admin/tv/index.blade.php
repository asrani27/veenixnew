@extends('layouts.app')

@section('title', 'Tv Management')

@section('header_title', 'Tv Management')

@section('content')
<div class="container mx-auto">
    <!-- Header with Add Tv Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Tv Management</h3>
            <p class="text-gray-600 mt-1">Manage your tv collection</p>
        </div>
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- TMDB Search Button -->
            <a href="{{ route('admin.tv.search-tmdb') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:ring-opacity-50">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Search TMDB
            </a>
            <!-- Add Tv Button -->
            {{-- <a href="{{ route('admin.tv.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Tv
            </a> --}}
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-3 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h3 class="text-lg font-medium text-gray-900">Tv List</h3>
                <!-- Search Form -->
                <form class="flex-1 sm:flex-initial sm:max-w-xs" method="GET" action="{{ route('admin.tv.index') }}">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search tv..." value="{{ request('search') }}"
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
                                Description</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Views</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($data ?? [] as $index => $item)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">{{
                                ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td class="px-6 py-2 whitespace-nowrap border-r border-gray-200">
                                <img src="{{ $item->local_poster_path ? asset('storage/' . $item->local_poster_path) : ($item->poster_path ? 'https://image.tmdb.org/t/p/w300' . $item->poster_path : asset('images/sample.jpg')) }}"
                                    alt="{{ $item->title ?? 'TV Series Image' }}"
                                    class="h-12 w-12 rounded-lg object-cover">
                            </td>
                            <td
                                class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                                <div>{{ $item->title ?? 'Untitled' }}</div>
                                <div class="text-xs text-gray-500">
                                    Genre : {{ (is_object($item->genres_data) && method_exists($item->genres_data,
                                    'pluck')) ? $item->genres_data->pluck('name')->join(', ') : '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Country : {{ (is_object($item->countries) && method_exists($item->countries,
                                    'pluck')) ? $item->countries->pluck('english_name')->join(', ') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-2 text-sm text-gray-600 max-w-xs truncate border-r border-gray-200">{{
                                $item->description ?? 'No
                                description available' }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">{{
                                $item->views ?? 0 }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.tv.seasons-page', $item->id) }}"
                                        class="inline-flex items-center p-2 bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                                        title="View Seasons">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.tv.destroy', $item->id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this tv?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center p-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                                            title="Delete TV Series">
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
                            <td colspan="7"
                                class="px-6 py-8 text-center text-sm text-gray-500 border-r border-gray-200">
                                No tv found.
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
    function importSeasonsAndEpisodes(tvId) {
    if (confirm('Are you sure you want to import all seasons and episodes for this TV series? This may take a while.')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        button.disabled = true;

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('CSRF token not found. Please refresh the page and try again.');
            button.innerHTML = originalHTML;
            button.disabled = false;
            return;
        }
        
        fetch(`/admin/tv/${tvId}/import-seasons`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Success: ${data.message}`);
                // Reload page to show updated data
                location.reload();
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while importing seasons and episodes.');
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalHTML;
            button.disabled = false;
        });
    }
}
</script>
@endsection
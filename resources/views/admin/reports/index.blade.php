@extends('layouts.app')

@section('title', 'Reports Management')

@section('header_title', 'Reports Management')

@section('content')
<div class="container mx-auto">
    <!-- Header with Stats -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Reports Management</h3>
            <p class="text-gray-600 mt-1">Manage user reports and issues</p>
        </div>
        <!-- Filter Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Status Filter Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" @click.away="open = false"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:ring-opacity-50">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filter: {{ request('status') ? ucfirst(request('status')) : 'All' }}
                    <svg class="w-4 h-4 ml-1 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="py-1">
                        <a href="{{ route('admin.reports.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors {{ request('status') == '' ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                All Reports
                            </div>
                        </a>
                        <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors {{ request('status') == 'pending' ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pending
                            </div>
                        </a>
                        <a href="{{ route('admin.reports.index', ['status' => 'reviewed']) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors {{ request('status') == 'reviewed' ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                Reviewed
                            </div>
                        </a>
                        <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors {{ request('status') == 'resolved' ? 'bg-purple-50 text-purple-700 font-medium' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Resolved
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="px-6 py-3 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <h3 class="text-lg font-medium text-gray-900">Report List</h3>
                <!-- Search Form -->
                <form class="flex-1 sm:flex-initial sm:max-w-xs" method="GET"
                    action="{{ route('admin.reports.index') }}">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search reports..." value="{{ request('search') }}"
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
                                No
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Movie
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Issue Type
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Description
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Reporter
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-r border-gray-200">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($reports as $index => $report)
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                                {{ ($reports->currentPage() - 1) * $reports->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap border-r border-gray-200">
                                <div class="flex items-center space-x-3">
                                    @if($report->movie && $report->movie->poster_url)
                                    <img src="{{ $report->movie->poster_url }}" alt="{{ $report->movie_title }}"
                                        class="h-12 w-12 rounded-lg object-cover shadow-sm">
                                    @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4"></path>
                                        </svg>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($report->movie)
                                            <a href="{{ route('movie.show', $report->movie->slug) }}" target="_blank"
                                                class="hover:text-blue-600 transition-colors">
                                                {{ $report->movie_title }}
                                            </a>
                                            @else
                                            {{ $report->movie_title }}
                                            @endif
                                        </div>
                                        @if($report->movie)
                                        <div class="text-xs text-gray-500">
                                            ID: {{ Str::limit($report->movie_id, 8) }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap border-r border-gray-200">
                                @php
                                $issueColors = [
                                'video_not_playing' => 'red',
                                'broken_link' => 'orange',
                                'poor_quality' => 'yellow',
                                'audio_problem' => 'blue',
                                'subtitle_issue' => 'green',
                                'other' => 'gray'
                                ];
                                $color = $issueColors[$report->issue_type] ?? 'gray';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ $report->issue_type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-2 border-r border-gray-200">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    <span class="line-clamp-2" title="{{ $report->description }}">
                                        {{ Str::limit($report->description, 100) }}
                                    </span>
                                    @if(Str::length($report->description) > 100)
                                    <button onclick="showFullDescription('{{ $report->id }}')"
                                        class="text-blue-600 hover:text-blue-800 text-xs mt-1">
                                        Read more
                                    </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap border-r border-gray-200">
                                @if($report->email)
                                <div class="flex items-center space-x-2">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <a href="mailto:{{ $report->email }}"
                                        class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                                        {{ Str::limit($report->email, 20) }}
                                    </a>
                                </div>
                                @else
                                <span class="text-sm text-gray-400">No email</span>
                                @endif
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap border-r border-gray-200">
                                @php
                                $statusColors = [
                                'pending' => 'yellow',
                                'reviewed' => 'blue',
                                'resolved' => 'green'
                                ];
                                $statusColor = $statusColors[$report->status] ?? 'gray';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                    @if($report->status == 'pending')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    @elseif($report->status == 'reviewed')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd"
                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    @endif
                                    {{ $report->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.reports.show', $report) }}"
                                        class="inline-flex items-center p-2 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                                        title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>

                                    <!-- Resolve Button -->
                                    @if($report->status !== 'resolved')
                                    <button type="button" onclick="markAsResolved({{ $report->id }})"
                                        class="inline-flex items-center p-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                                        title="Mark as Resolved">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    @endif

                                    <!-- Delete Button -->
                                    <button type="button" onclick="deleteReport({{ $report->id }})"
                                        class="inline-flex items-center p-2 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 text-white rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200"
                                        title="Delete Report">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if ($reports->count() == 0)
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span class="font-medium">No reports found</span>
                                    <span class="text-xs mt-1">There are no reports to display at this time.</span>
                                </div>
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
                    <span class="font-medium">{{ $reports->firstItem() }}</span>
                    to
                    <span class="font-medium">{{ $reports->lastItem() }}</span>
                    of
                    <span class="font-medium">{{ $reports->total() }}</span>
                    results
                </div>

                <div class="flex space-x-2">
                    @if ($reports->hasPages())
                    <!-- Previous button -->
                    <a href="{{ $reports->previousPageUrl() }}"
                        class="{{ $reports->onFirstPage() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100' }} relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md transition-colors"
                        {{ $reports->onFirstPage() ? 'disabled' : '' }}>
                        Previous
                    </a>

                    <!-- Page numbers -->
                    @foreach ($elements = $reports->links()->elements as $element)
                    @if (is_string($element))
                    <span
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300">
                        {!! $element !!}
                    </span>
                    @endif

                    @if (is_array($element))
                    @foreach ($element as $page => $url)
                    @if ($page == $reports->currentPage())
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
                    <a href="{{ $reports->nextPageUrl() }}"
                        class="{{ $reports->hasMorePages() ? 'hover:bg-gray-100' : 'opacity-50 cursor-not-allowed' }} relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md transition-colors"
                        {{ !$reports->hasMorePages() ? 'disabled' : '' }}>
                        Next
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Full Description Modal -->
<div x-data="{ showModal: false, description: '' }" x-show="showModal"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
            @click="showModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Report Description</h3>
                <button type="button" @click="showModal = false"
                    class="text-gray-400 hover:text-gray-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="mt-2">
                <p class="text-sm text-gray-600 whitespace-pre-wrap" x-text="description"></p>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" @click="showModal = false"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
    function markAsResolved(reportId) {
        if (confirm('Are you sure you want to mark this report as resolved?')) {
            fetch(`/admin/reports/${reportId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: 'resolved',
                    admin_notes: 'Marked as resolved by admin'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating report status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating report status. Please try again.');
            });
        }
    }

    function deleteReport(reportId) {
        if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
            fetch(`/admin/reports/${reportId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting report: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting report. Please try again.');
            });
        }
    }

    function showFullDescription(reportId) {
        // Find the report data from the table
        const rows = document.querySelectorAll('tbody tr');
        let description = '';
        
        rows.forEach(row => {
            const viewButton = row.querySelector('a[href*="/admin/reports/"]');
            if (viewButton && viewButton.href.includes(`/admin/reports/${reportId}`)) {
                const descCell = row.querySelector('td:nth-child(4)');
                if (descCell) {
                    // Get the full description from the title attribute
                    const descElement = descCell.querySelector('.line-clamp-2');
                    if (descElement) {
                        description = descElement.getAttribute('title') || descElement.textContent;
                    }
                }
            }
        });
        
        if (description) {
            // Try to use Alpine.js to show the modal
            const modal = document.querySelector('[x-data*="showModal"]');
            if (modal && modal.__x) {
                modal.__x.$data.showModal = true;
                modal.__x.$data.description = description;
            } else {
                // Fallback: create a simple modal if Alpine.js is not ready
                showSimpleModal(description);
            }
        } else {
            alert('Description not found.');
        }
    }

    function showSimpleModal(description) {
        // Create a simple modal as fallback
        const modalHtml = `
            <div id="simpleModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                <div style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
                    <h3 style="margin: 0 0 15px 0; color: #333;">Report Description</h3>
                    <p style="margin: 0 0 20px 0; color: #666; white-space: pre-wrap;">${description}</p>
                    <button onclick="document.getElementById('simpleModal').remove()" style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Close</button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    // Initialize tooltips if jQuery is available
    if (typeof $ !== 'undefined') {
        $(document).ready(function() {
            $('[title]').tooltip();
        });
    }
</script>
@endpush

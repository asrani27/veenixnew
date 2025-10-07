@extends('layouts.app')

@section('title', 'Season Episodes')

@section('content')
<div class="container mx-auto">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Season {{ $season->season_number }} Episodes</h3>
            <p class="text-gray-600 mt-1">{{ $tv->title ?? 'TV Series' }}</p>
        </div>
        <a href="{{ route('admin.tv.seasons-page', $tv->id) }}"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Seasons
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="p-6">
            <!-- Season Info -->
            <div class="flex flex-col md:flex-row gap-6 mb-8">
                <div class="md:w-32 flex-shrink-0">
                    <img src="{{ $season->local_poster_path ? asset('storage/' . $season->local_poster_path) : ($season->poster_path ? 'https://image.tmdb.org/t/p/w200' . $season->poster_path : asset('images/sample.jpg')) }}"
                        alt="{{ $season->name ?? 'Season Image' }}" class="w-full rounded-lg shadow-md object-cover">
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $season->name ?? 'Season ' .
                        $season->season_number }}</h2>
                    <p class="text-gray-600 mb-4">{{ $season->overview ?? 'No overview available' }}</p>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            ⭐ {{ number_format($season->vote_average ?? 0, 1) }}
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $season->episode_count ?? 0 }} Episodes
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Air Date: {{ $season->air_date ? \Carbon\Carbon::parse($season->air_date)->format('M d, Y')
                            : 'N/A' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-900">Season Number:</span>
                            <p class="text-gray-600">{{ $season->season_number }}</p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">Episode Count:</span>
                            <p class="text-gray-600">{{ $season->episode_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Episodes List -->
            <div class="border-t pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Episodes</h3>
                    <a href="{{ route('admin.tv.episode.create', ['tv' => $tv->id, 'season' => $season->season_number]) }}"
                        class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-opacity-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Episode
                    </a>
                </div>

                @if($episodes->count() > 0)
                <div class="space-y-4">
                    @foreach($episodes as $episode)
                    <div
                        class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border hover:shadow-md transition-shadow duration-300">
                        <div class="w-24 h-16 flex-shrink-0 bg-gray-200 rounded-lg overflow-hidden">
                            <img src="{{ $episode->local_still_path ? asset('storage/' . $episode->local_still_path) : ($episode->still_path ? 'https://image.tmdb.org/t/p/w200' . $episode->still_path : asset('images/sample.jpg')) }}"
                                alt="{{ $episode->name ?? 'Episode Image' }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-1">Episode {{ $episode->episode_number }}: {{
                                $episode->name ?? 'Untitled' }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $episode->overview ?? 'No overview available' }}
                            </p>
                            
                            <div class="flex flex-wrap gap-3 text-xs text-gray-500">
                                <span>Air Date: {{ $episode->air_date ?
                                    \Carbon\Carbon::parse($episode->air_date)->format('M d, Y') : 'N/A' }}</span>
                                <span>Runtime: {{ $episode->runtime ? $episode->runtime . ' min' : 'N/A' }}</span>
                                <span>⭐ {{ number_format($episode->vote_average ?? 0, 1) }}</span>
                                @if(!empty($episode->file))
                                    <span class="text-blue-600">📁 {{ basename($episode->file) }}</span>
                                @endif
                                @if(!empty($episode->hls_playlist_path))
                                    <span class="text-green-600">🎬 HLS Available</span>
                                @endif
                                <span class="{{ $episode->publish ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $episode->publish ? '✓ Published' : '✗ Unpublished' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="flex items-center gap-2">
                                <!-- Status Indicators -->
                                <div class="flex items-center gap-2 text-xs">
                                    <!-- Views -->
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>{{ number_format($episode->views ?? 0) }}</span>
                                    </div>
                                    
                                    <!-- Upload Status -->
                                    <div>
                                        @if(!empty($episode->file))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                ✓
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                –
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- HLS Status -->
                                    <div>
                                        @if(!empty($episode->hls_playlist_path))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                HLS
                                            </span>
                                        @elseif(!empty($episode->file))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                –
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex items-center gap-1">
                                    <!-- Publish Toggle -->
                                    <form action="{{ route('admin.tv.episode.toggle-publish', ['tv' => $tv->id, 'season' => $season->season_number, 'episode' => $episode->episode_number]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-2 py-1 {{ $episode->publish ? 'bg-green-100 hover:bg-green-200 text-green-700' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }} text-xs font-medium rounded transition-colors"
                                            title="{{ $episode->publish ? 'Unpublish Episode' : 'Publish Episode' }}">
                                            @if($episode->publish)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @else
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                    @if(!empty($episode->file) || !empty($episode->hls_playlist_path))
                                        <a href="{{ route('tv.episode.stream', ['slug' => $episode->tv->slug, 'season_number' => $episode->season_number, 'episode_number' => $episode->episode_number]) }}" target="_blank"
                                            class="px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 text-xs font-medium rounded transition-colors"
                                            title="Stream Episode">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.tv.episode.edit', ['tv' => $tv->id, 'season' => $season->season_number, 'episode' => $episode->episode_number]) }}"
                                        class="px-2 py-1 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 text-xs font-medium rounded transition-colors"
                                        title="Edit Episode">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.tv.episode.destroy', ['tv' => $tv->id, 'season' => $season->season_number, 'episode' => $episode->episode_number]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this episode?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded transition-colors"
                                            title="Delete Episode">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No episodes found</h3>
                    <p class="mt-1 text-sm text-gray-500">This season doesn't have any episodes yet.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Season - ' . $tv->title)

@section('content')
<div class="container mx-auto">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Edit Season</h3>
            <p class="text-gray-600 mt-1">{{ $tv->title }} - Season {{ $season->season_number }}</p>
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
            <form action="{{ route('admin.tv.season.update', ['tv' => $tv->id, 'season' => $season->season_number]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Form Fields -->
                    <div class="space-y-6">
                        <!-- Season Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Season Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $season->name) }}"
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('overview', $season->overview) }}</textarea>
                            @error('overview')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Air Date -->
                        <div>
                            <label for="air_date" class="block text-sm font-medium text-gray-700 mb-2">Air Date</label>
                            <input type="date" id="air_date" name="air_date" value="{{ old('air_date', $season->air_date) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('air_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Episode Count -->
                        <div>
                            <label for="episode_count" class="block text-sm font-medium text-gray-700 mb-2">Episode Count</label>
                            <input type="number" id="episode_count" name="episode_count" value="{{ old('episode_count', $season->episode_count) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="0">
                            @error('episode_count')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Vote Average -->
                        <div>
                            <label for="vote_average" class="block text-sm font-medium text-gray-700 mb-2">Vote Average (0-10)</label>
                            <input type="number" id="vote_average" name="vote_average" value="{{ old('vote_average', $season->vote_average) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                step="0.1" min="0" max="10">
                            @error('vote_average')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Vote Count -->
                        <div>
                            <label for="vote_count" class="block text-sm font-medium text-gray-700 mb-2">Vote Count</label>
                            <input type="number" id="vote_count" name="vote_count" value="{{ old('vote_count', $season->vote_count) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="0">
                            @error('vote_count')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column - Poster Upload and Preview -->
                    <div class="space-y-6">
                        <!-- Poster Upload -->
                        <div>
                            <label for="poster" class="block text-sm font-medium text-gray-700 mb-2">Season Poster</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="poster" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a new poster</span>
                                            <input id="poster" name="poster" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg,image/gif">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            @error('poster')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Poster Preview -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Poster</label>
                            <div class="flex justify-center">
                                <div class="w-48 h-72 bg-gray-100 rounded-lg overflow-hidden shadow-md">
                                    <img src="{{ $season->local_poster_path ? asset('storage/' . $season->local_poster_path) : ($season->poster_path ? 'https://image.tmdb.org/t/p/w300' . $season->poster_path : asset('images/sample.jpg')) }}"
                                        alt="{{ $season->name ?? 'Season ' . $season->season_number }}"
                                        class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>

                        <!-- Season Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Season Information</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <p><span class="font-medium">Season Number:</span> {{ $season->season_number }}</p>
                                <p><span class="font-medium">TMDB ID:</span> {{ $season->tmdb_id ?? 'N/A' }}</p>
                                <p><span class="font-medium">Created:</span> {{ $season->created_at->format('M d, Y H:i') }}</p>
                                <p><span class="font-medium">Updated:</span> {{ $season->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.tv.seasons-page', $tv->id) }}"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V2"></path>
                        </svg>
                        Update Season
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

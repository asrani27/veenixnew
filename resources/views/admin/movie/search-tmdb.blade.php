@extends('layouts.app')

@section('title', 'Search Movies from TMDB')

@section('content')
<div class="container mx-auto">
    <!-- Header with Back Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Search Movies from TMDB</h3>
            <p class="text-gray-600 mt-1">Browse and import movies from The Movie Database</p>
        </div>
        <a href="{{ route('admin.movies.index') }}"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Movies
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg border border-gray-200">
        <div class="p-6">
            <!-- Search Form -->
            <div class="flex flex-col lg:flex-row gap-4 mb-6">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" id="searchInput"
                            class="w-full pl-10 pr-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition-colors"
                            placeholder="Search for movies...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="lg:w-64">
                    <select id="categorySelect"
                        class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition-colors">
                        <option value="popular">Popular Movies</option>
                        <option value="top_rated">Top Rated</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="now_playing">Now Playing</option>
                    </select>
                </div>
                <button type="button" id="searchBtn"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:ring-opacity-50">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Search</span>
                </button>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="text-center py-8 hidden">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500"></div>
                <p class="mt-4 text-gray-600">Loading movies from TMDB...</p>
            </div>

            <!-- Error Alert -->
            <div id="errorAlert" class="hidden mb-6">
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700" id="errorMessage"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Alert -->
            <div id="successAlert" class="hidden mb-6">
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700" id="successMessage"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Movies Grid -->
            <div id="moviesGrid"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                <!-- Movies will be loaded here -->
            </div>

            <!-- Pagination -->
            <div id="pagination" class="flex justify-center mt-8">
                <!-- Pagination will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Movie Details Modal -->
<div id="movieDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900" id="movieDetailsModalLabel">Movie Details</h3>
            <button onclick="closeMovieDetailsModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <div class="p-6" id="movieDetailsContent">
            <!-- Movie details will be loaded here -->
        </div>
        <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
            <button onclick="closeMovieDetailsModal()"
                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium rounded-lg transition-colors">
                Close
            </button>
            <button id="importMovieBtn"
                class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Import Movie
            </button>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    // Global function to close modal
function closeMovieDetailsModal() {
    const modal = document.getElementById('movieDetailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

$(document).ready(function() {
    console.log('Document ready, jQuery version:', $.fn.jquery);
    console.log('Checking if all required elements exist...');
    console.log('searchInput:', $('#searchInput').length);
    console.log('searchBtn:', $('#searchBtn').length);
    console.log('categorySelect:', $('#categorySelect').length);
    console.log('moviesGrid:', $('#moviesGrid').length);
    console.log('loadingSpinner:', $('#loadingSpinner').length);
    console.log('errorAlert:', $('#errorAlert').length);
    console.log('successAlert:', $('#successAlert').length);
    
    let currentPage = 1;
    let currentQuery = '';
    let currentType = 'popular';
    let selectedMovieId = null;

    // Search button click
    $('#searchBtn').click(function() {
        currentQuery = $('#searchInput').val().trim();
        if (currentQuery.length >= 2) {
            currentPage = 1;
            searchMovies();
        } else {
            showError('Please enter at least 2 characters to search.');
        }
    });

    // Enter key in search input
    $('#searchInput').keypress(function(e) {
        if (e.which === 13) {
            $('#searchBtn').click();
        }
    });

    // Category select change
    $('#categorySelect').change(function() {
        currentType = $(this).val();
        currentQuery = '';
        $('#searchInput').val('');
        currentPage = 1;
        loadPopularMovies();
    });

    // Load popular movies on page load
    console.log('Page loaded, starting to load popular movies...');
    loadPopularMovies();

    // Import movie button click
    $('#importMovieBtn').click(function() {
        if (selectedMovieId) {
            importMovie(selectedMovieId);
        }
    });

    function searchMovies() {
        showLoading();
        $.ajax({
            url: '{{ route("admin.movies.search-tmdb-api") }}',
            method: 'POST',
            data: {
                query: currentQuery,
                page: currentPage,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    displayMovies(response.data);
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr) {
                showError('Error searching movies. Please try again.');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function loadPopularMovies() {
        console.log('loadPopularMovies called with type:', currentType, 'page:', currentPage);
        console.log('Route URL:', '{{ route("admin.movies.popular-tmdb") }}');
        showLoading();
        $.ajax({
            url: '{{ route("admin.movies.popular-tmdb") }}',
            method: 'GET',
            data: {
                type: currentType,
                page: currentPage
            },
            success: function(response) {
                console.log('AJAX success response:', response);
                if (response.success) {
                    console.log('Loading movies, count:', response.data.results ? response.data.results.length : 0);
                    displayMovies(response.data);
                } else {
                    console.log('API returned error:', response.message);
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', xhr, status, error);
                console.error('Response text:', xhr.responseText);
                showError('Error loading movies. Please try again.');
            },
            complete: function() {
                console.log('AJAX completed');
                hideLoading();
            }
        });
    }

    function displayMovies(data) {
        const movies = data.results || [];
        const moviesGrid = $('#moviesGrid');
        
        moviesGrid.empty();

        if (movies.length === 0) {
            moviesGrid.html('<div class="col-span-full text-center py-8"><p class="text-gray-500">No movies found.</p></div>');
            return;
        }

        movies.forEach(function(movie) {
            const posterUrl = movie.poster_path 
                ? 'https://image.tmdb.org/t/p/w300' + movie.poster_path 
                : 'https://via.placeholder.com/300x450?text=No+Image';
            
            const movieCard = `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="aspect-[2/3] overflow-hidden">
                        <img src="${posterUrl}" alt="${movie.title}" class="w-full h-full object-cover">
                    </div>
                    <div class="p-2">
                        <h3 class="font-semibold text-gray-900 text-xs mb-1 line-clamp-2 leading-tight">${movie.title}</h3>
                        <p class="text-xs text-gray-500 mb-2">
                            ${movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A'} | 
                            ⭐ ${movie.vote_average.toFixed(1)}
                        </p>
                        <div class="flex gap-1">
                            <button class="view-details-btn flex-1 px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded transition-colors" data-movie-id="${movie.id}">
                                <svg class="w-2 h-2 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Details
                            </button>
                            <button class="import-btn flex-1 px-2 py-1 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white text-xs font-medium rounded transition-all duration-200" data-movie-id="${movie.id}">
                                <svg class="w-2 h-2 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                                Import
                            </button>
                        </div>
                    </div>
                </div>
            `;
            moviesGrid.append(movieCard);
        });

        // Add click handlers
        $('.view-details-btn').click(function() {
            const movieId = $(this).data('movie-id');
            showMovieDetails(movieId);
        });

        $('.import-btn').click(function() {
            const movieId = $(this).data('movie-id');
            importMovie(movieId);
        });

        // Show pagination
        showPagination(data);
    }

    function showMovieDetails(movieId) {
        showLoading();
        $.ajax({
            url: '{{ route("admin.movies.tmdb-details", ["tmdbId" => ":id"]) }}'.replace(':id', movieId),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const movie = response.data;
                    const posterUrl = movie.poster_url || 'https://via.placeholder.com/300x450?text=No+Image';
                    const backdropUrl = movie.backdrop_url || 'https://via.placeholder.com/800x400?text=No+Backdrop';
                    
                    const detailsHtml = `
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <img src="${posterUrl}" alt="${movie.title}" class="w-full rounded-lg shadow-md">
                            </div>
                            <div class="md:col-span-2 space-y-4">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">${movie.title}</h2>
                                    <p class="text-gray-600">${movie.original_title} (${new Date(movie.release_date).getFullYear()})</p>
                                </div>
                                
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        ⭐ ${movie.vote_average.toFixed(1)}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        ${movie.runtime} min
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        ${movie.status}
                                    </span>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Overview</h3>
                                    <p class="text-gray-700 leading-relaxed">${movie.overview || 'No overview available.'}</p>
                                    ${movie.tagline ? `<p class="text-gray-600 italic mt-2">"${movie.tagline}"</p>` : ''}
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-900">Genres:</span>
                                        <p class="text-gray-600">${movie.genres ? movie.genres.map(g => g.name).join(', ') : 'N/A'}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900">Release Date:</span>
                                        <p class="text-gray-600">${movie.release_date || 'N/A'}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900">Original Language:</span>
                                        <p class="text-gray-600">${movie.original_language}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900">Popularity:</span>
                                        <p class="text-gray-600">${movie.popularity.toFixed(1)}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#movieDetailsContent').html(detailsHtml);
                    selectedMovieId = movieId;
                    
                    // Show modal
                    const modal = document.getElementById('movieDetailsModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr) {
                showError('Error loading movie details.');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function importMovie(movieId) {
        showLoading();
        $.ajax({
            url: '{{ route("admin.movies.import-from-tmdb", ["tmdbId" => ":id"]) }}'.replace(':id', movieId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showSuccess('Movie imported successfully!');
                    closeMovieDetailsModal();
                    // Update import button for this movie
                    const importBtn = $(`.import-btn[data-movie-id="${movieId}"]`);
                    importBtn.prop('disabled', true)
                        .removeClass('from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600')
                        .addClass('bg-gray-300 cursor-not-allowed')
                        .html('<svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Imported');
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showError(response.message || 'Error importing movie.');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function showPagination(data) {
        const pagination = $('#pagination');
        pagination.empty();

        if (data.page <= 1 && !data.results || data.results.length === 0) {
            return;
        }

        let paginationHtml = '<nav class="flex items-center space-x-1">';
        
        // Previous button
        if (data.page > 1) {
            paginationHtml += `
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" data-page="${data.page - 1}">
                    Previous
                </button>
            `;
        }

        // Page numbers (simplified)
        const startPage = Math.max(1, data.page - 2);
        const endPage = Math.min(data.total_pages, data.page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === data.page 
                ? 'z-10 bg-purple-50 border-purple-500 text-purple-600' 
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50';
            
            paginationHtml += `
                <button class="px-3 py-2 text-sm font-medium border ${activeClass} disabled:opacity-50 disabled:cursor-not-allowed" data-page="${i}">
                    ${i}
                </button>
            `;
        }

        // Next button
        if (data.page < data.total_pages) {
            paginationHtml += `
                <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed" data-page="${data.page + 1}">
                    Next
                </button>
            `;
        }

        paginationHtml += '</nav>';
        pagination.html(paginationHtml);

        // Add click handlers for pagination
        pagination.find('button').click(function(e) {
            e.preventDefault();
            if (!$(this).prop('disabled')) {
                currentPage = parseInt($(this).data('page'));
                
                if (currentQuery) {
                    searchMovies();
                } else {
                    loadPopularMovies();
                }
            }
        });
    }

    function showLoading() {
        $('#loadingSpinner').removeClass('hidden');
        $('#errorAlert').addClass('hidden');
        $('#successAlert').addClass('hidden');
    }

    function hideLoading() {
        $('#loadingSpinner').addClass('hidden');
    }

    function showError(message) {
        $('#errorMessage').text(message);
        $('#errorAlert').removeClass('hidden');
        $('#successAlert').addClass('hidden');
    }

    function showSuccess(message) {
        $('#successMessage').text(message);
        $('#successAlert').removeClass('hidden');
        $('#errorAlert').addClass('hidden');
    }
});

</script>
@endpush
<?php

namespace Tests;

use App\Services\TmdbService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TmdbApiTest extends TestCase
{
    protected $tmdbService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmdbService = new TmdbService();
    }

    /**
     * Test TMDB API key is configured
     */
    public function test_tmdb_api_key_is_configured()
    {
        $apiKey = env('TMDB_API_KEY');
        $this->assertNotNull($apiKey, 'TMDB API key is not configured in .env file');
        $this->assertNotEmpty($apiKey, 'TMDB API key is empty');
        
        echo "✓ TMDB API key is configured: " . substr($apiKey, 0, 8) . "...\n";
    }

    /**
     * Test basic TMDB API connection
     */
    public function test_basic_tmdb_api_connection()
    {
        try {
            // Test the configuration endpoint which is lightweight
            $response = $this->tmdbService->makeRequest('/configuration');
            
            $this->assertIsArray($response, 'TMDB API response should be an array');
            $this->assertArrayHasKey('images', $response, 'TMDB configuration should contain images data');
            $this->assertArrayHasKey('base_url', $response['images'], 'TMDB images configuration should contain base_url');
            
            echo "✓ Basic TMDB API connection successful\n";
            echo "  - Image base URL: " . $response['images']['base_url'] . "\n";
            echo "  - Secure image base URL: " . $response['images']['secure_base_url'] . "\n";
            
        } catch (\Exception $e) {
            $this->fail("TMDB API connection failed: " . $e->getMessage());
        }
    }

    /**
     * Test popular movies endpoint
     */
    public function test_get_popular_movies()
    {
        try {
            $response = $this->tmdbService->getPopularMovies(1);
            
            $this->assertIsArray($response, 'Popular movies response should be an array');
            $this->assertArrayHasKey('results', $response, 'Popular movies response should contain results');
            $this->assertArrayHasKey('page', $response, 'Popular movies response should contain page info');
            $this->assertArrayHasKey('total_results', $response, 'Popular movies response should contain total_results');
            $this->assertArrayHasKey('total_pages', $response, 'Popular movies response should contain total_pages');
            
            $this->assertIsArray($response['results'], 'Results should be an array');
            $this->assertGreaterThan(0, count($response['results']), 'Should return at least one movie');
            
            // Check structure of first movie
            $firstMovie = $response['results'][0];
            $this->assertArrayHasKey('id', $firstMovie, 'Movie should have an ID');
            $this->assertArrayHasKey('title', $firstMovie, 'Movie should have a title');
            $this->assertArrayHasKey('overview', $firstMovie, 'Movie should have an overview');
            $this->assertArrayHasKey('poster_path', $firstMovie, 'Movie should have a poster_path');
            $this->assertArrayHasKey('vote_average', $firstMovie, 'Movie should have a vote_average');
            
            echo "✓ Popular movies endpoint successful\n";
            echo "  - Retrieved " . count($response['results']) . " movies\n";
            echo "  - Total results: " . $response['total_results'] . "\n";
            echo "  - Sample movie: " . $firstMovie['title'] . " (ID: " . $firstMovie['id'] . ")\n";
            
        } catch (\Exception $e) {
            $this->fail("Get popular movies failed: " . $e->getMessage());
        }
    }

    /**
     * Test movie details endpoint
     */
    public function test_get_movie_details()
    {
        try {
            // First get a popular movie to use its ID
            $popularMovies = $this->tmdbService->getPopularMovies(1);
            $movieId = $popularMovies['results'][0]['id'];
            
            $response = $this->tmdbService->getMovieDetails($movieId);
            
            $this->assertIsArray($response, 'Movie details response should be an array');
            $this->assertArrayHasKey('id', $response, 'Movie details should contain ID');
            $this->assertArrayHasKey('title', $response, 'Movie details should contain title');
            $this->assertArrayHasKey('overview', $response, 'Movie details should contain overview');
            $this->assertArrayHasKey('release_date', $response, 'Movie details should contain release_date');
            $this->assertArrayHasKey('runtime', $response, 'Movie details should contain runtime');
            $this->assertArrayHasKey('credits', $response, 'Movie details should contain credits');
            $this->assertArrayHasKey('videos', $response, 'Movie details should contain videos');
            $this->assertArrayHasKey('images', $response, 'Movie details should contain images');
            
            // Check credits structure
            $this->assertArrayHasKey('cast', $response['credits'], 'Credits should contain cast');
            $this->assertArrayHasKey('crew', $response['credits'], 'Credits should contain crew');
            
            echo "✓ Movie details endpoint successful\n";
            echo "  - Movie: " . $response['title'] . "\n";
            echo "  - Release Date: " . $response['release_date'] . "\n";
            echo "  - Runtime: " . $response['runtime'] . " minutes\n";
            echo "  - Cast count: " . count($response['credits']['cast']) . "\n";
            
        } catch (\Exception $e) {
            $this->fail("Get movie details failed: " . $e->getMessage());
        }
    }

    /**
     * Test search movies endpoint
     */
    public function test_search_movies()
    {
        try {
            $response = $this->tmdbService->searchMovies('Batman', 1);
            
            $this->assertIsArray($response, 'Search movies response should be an array');
            $this->assertArrayHasKey('results', $response, 'Search response should contain results');
            $this->assertArrayHasKey('total_results', $response, 'Search response should contain total_results');
            
            $this->assertIsArray($response['results'], 'Results should be an array');
            $this->assertGreaterThan(0, count($response['results']), 'Search should return at least one result');
            
            // Check that results contain the search term
            $foundBatman = false;
            foreach ($response['results'] as $movie) {
                if (stripos($movie['title'], 'batman') !== false || stripos($movie['original_title'], 'batman') !== false) {
                    $foundBatman = true;
                    break;
                }
            }
            $this->assertTrue($foundBatman, 'Search results should contain movies with "Batman" in the title');
            
            echo "✓ Search movies endpoint successful\n";
            echo "  - Search term: 'Batman'\n";
            echo "  - Results found: " . count($response['results']) . "\n";
            echo "  - Total results: " . $response['total_results'] . "\n";
            
        } catch (\Exception $e) {
            $this->fail("Search movies failed: " . $e->getMessage());
        }
    }

    /**
     * Test movie genres endpoint
     */
    public function test_get_movie_genres()
    {
        try {
            $response = $this->tmdbService->getMovieGenres();
            
            $this->assertIsArray($response, 'Movie genres response should be an array');
            $this->assertGreaterThan(0, count($response), 'Should return at least one genre');
            
            // Check structure of first genre
            $firstGenre = $response[0];
            $this->assertArrayHasKey('id', $firstGenre, 'Genre should have an ID');
            $this->assertArrayHasKey('name', $firstGenre, 'Genre should have a name');
            
            echo "✓ Movie genres endpoint successful\n";
            echo "  - Genres retrieved: " . count($response) . "\n";
            echo "  - Sample genres: " . $response[0]['name'] . ", " . $response[1]['name'] . ", " . $response[2]['name'] . "\n";
            
        } catch (\Exception $e) {
            $this->fail("Get movie genres failed: " . $e->getMessage());
        }
    }

    /**
     * Test TV series endpoints
     */
    public function test_tv_series_endpoints()
    {
        try {
            // Test popular TV series
            $popularTv = $this->tmdbService->getPopularTv(1);
            
            $this->assertIsArray($popularTv, 'Popular TV response should be an array');
            $this->assertArrayHasKey('results', $popularTv, 'Popular TV response should contain results');
            $this->assertGreaterThan(0, count($popularTv['results']), 'Should return at least one TV series');
            
            // Test TV series details
            $tvId = $popularTv['results'][0]['id'];
            $tvDetails = $this->tmdbService->getTvDetails($tvId);
            
            $this->assertIsArray($tvDetails, 'TV details response should be an array');
            $this->assertArrayHasKey('id', $tvDetails, 'TV details should contain ID');
            $this->assertArrayHasKey('name', $tvDetails, 'TV details should contain name');
            $this->assertArrayHasKey('first_air_date', $tvDetails, 'TV details should contain first_air_date');
            $this->assertArrayHasKey('number_of_seasons', $tvDetails, 'TV details should contain number_of_seasons');
            
            echo "✓ TV series endpoints successful\n";
            echo "  - Popular TV series: " . count($popularTv['results']) . " retrieved\n";
            echo "  - Sample TV series: " . $tvDetails['name'] . " (Seasons: " . $tvDetails['number_of_seasons'] . ")\n";
            
        } catch (\Exception $e) {
            $this->fail("TV series endpoints failed: " . $e->getMessage());
        }
    }

    /**
     * Test image URL generation
     */
    public function test_image_url_generation()
    {
        try {
            // Test poster URL
            $posterPath = '/kqjL17yufvn9OVLyXYpvtyrFfak.jpg'; // Example poster path
            $posterUrl = $this->tmdbService->getImageUrl($posterPath, 'w500');
            
            $this->assertStringContainsString('image.tmdb.org', $posterUrl, 'Poster URL should contain TMDB image domain');
            $this->assertStringContainsString('w500', $posterUrl, 'Poster URL should contain size specification');
            $this->assertStringEndsWith($posterPath, $posterUrl, 'Poster URL should end with the poster path');
            
            // Test backdrop URL
            $backdropPath = '/xJHokMbljvjADYdit5fK5VQsXEG.jpg'; // Example backdrop path
            $backdropUrl = $this->tmdbService->getBackdropUrl($backdropPath, 'original');
            
            $this->assertStringContainsString('image.tmdb.org', $backdropUrl, 'Backdrop URL should contain TMDB image domain');
            $this->assertStringContainsString('original', $backdropUrl, 'Backdrop URL should contain size specification');
            $this->assertStringEndsWith($backdropPath, $backdropUrl, 'Backdrop URL should end with the backdrop path');
            
            echo "✓ Image URL generation successful\n";
            echo "  - Poster URL: " . $posterUrl . "\n";
            echo "  - Backdrop URL: " . $backdropUrl . "\n";
            
        } catch (\Exception $e) {
            $this->fail("Image URL generation failed: " . $e->getMessage());
        }
    }

    /**
     * Test error handling
     */
    public function test_error_handling()
    {
        try {
            // Test with invalid movie ID
            $this->expectException(\Exception::class);
            $this->tmdbService->getMovieDetails(999999999);
            
        } catch (\Exception $e) {
            $this->assertStringContainsString('not found', strtolower($e->getMessage()), 'Should handle 404 errors properly');
            echo "✓ Error handling working correctly\n";
            echo "  - Properly handled invalid movie ID: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Test data formatting
     */
    public function test_data_formatting()
    {
        try {
            // Get a sample movie
            $popularMovies = $this->tmdbService->getPopularMovies(1);
            $movieData = $popularMovies['results'][0];
            
            // Test movie data formatting
            $formattedData = $this->tmdbService->formatMovieData($movieData);
            
            $this->assertIsArray($formattedData, 'Formatted movie data should be an array');
            $this->assertArrayHasKey('tmdb_id', $formattedData, 'Formatted data should contain tmdb_id');
            $this->assertArrayHasKey('title', $formattedData, 'Formatted data should contain title');
            $this->assertArrayHasKey('overview', $formattedData, 'Formatted data should contain overview');
            $this->assertArrayHasKey('poster_path', $formattedData, 'Formatted data should contain poster_path');
            $this->assertArrayHasKey('vote_average', $formattedData, 'Formatted data should contain vote_average');
            
            echo "✓ Data formatting working correctly\n";
            echo "  - Formatted movie: " . $formattedData['title'] . "\n";
            echo "  - TMDB ID: " . $formattedData['tmdb_id'] . "\n";
            echo "  - Vote Average: " . $formattedData['vote_average'] . "\n";
            
        } catch (\Exception $e) {
            $this->fail("Data formatting test failed: " . $e->getMessage());
        }
    }

    /**
     * Run all tests and provide a summary
     */
    public function run_comprehensive_tmdb_test()
    {
        echo "=== TMDB API Comprehensive Test ===\n\n";
        
        $tests = [
            'test_tmdb_api_key_is_configured',
            'test_basic_tmdb_api_connection',
            'test_get_popular_movies',
            'test_get_movie_details',
            'test_search_movies',
            'test_get_movie_genres',
            'test_tv_series_endpoints',
            'test_image_url_generation',
            'test_error_handling',
            'test_data_formatting'
        ];
        
        $passed = 0;
        $failed = 0;
        
        foreach ($tests as $test) {
            try {
                echo "Running {$test}...\n";
                $this->$test();
                $passed++;
                echo "\n";
            } catch (\Exception $e) {
                echo "❌ FAILED: " . $e->getMessage() . "\n\n";
                $failed++;
            }
        }
        
        echo "=== Test Summary ===\n";
        echo "Passed: {$passed}\n";
        echo "Failed: {$failed}\n";
        echo "Total: " . ($passed + $failed) . "\n";
        
        if ($failed === 0) {
            echo "\n🎉 All TMDB API tests passed successfully!\n";
        } else {
            echo "\n⚠️  {$failed} test(s) failed. Please check the errors above.\n";
        }
        
        return $failed === 0;
    }
}

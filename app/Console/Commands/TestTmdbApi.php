<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tests\TmdbApiTest;
use Illuminate\Support\Facades\App;

class TestTmdbApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmdb:test {--quick : Run only basic connection tests}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test TMDB API connection and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting TMDB API Test...');
        $this->line('========================');

        // Ensure environment variables are loaded properly for console commands
        if (!env('TMDB_API_KEY')) {
            if (file_exists(base_path() . '/.env')) {
                $lines = file(base_path() . '/.env');
                foreach ($lines as $line) {
                    if (strpos(trim($line), 'TMDB_API_KEY=') === 0) {
                        $apiKey = trim(substr(trim($line), 13)); // Remove 'TMDB_API_KEY=' (13 characters)
                        putenv('TMDB_API_KEY=' . $apiKey);
                        $_ENV['TMDB_API_KEY'] = $apiKey;
                        break;
                    }
                }
            }
        }

        // Create the TMDB service directly
        $tmdbService = new \App\Services\TmdbService();
        
        // Create a simple test runner
        $testRunner = new class($tmdbService) {
            protected $tmdbService;
            
            public function __construct($tmdbService) {
                $this->tmdbService = $tmdbService;
            }
            
            public function testApiKeyConfiguration() {
                // The environment should already be loaded by Laravel, but let's make sure
                $apiKey = env('TMDB_API_KEY');
                if (!$apiKey) {
                    throw new \Exception('TMDB API key is not configured in .env file');
                }
                if (empty($apiKey)) {
                    throw new \Exception('TMDB API key is empty');
                }
                
                echo "✓ TMDB API key is configured: " . substr($apiKey, 0, 8) . "...\n";
            }
            
            public function testBasicConnection() {
                // Use a public method instead of the protected makeRequest
                $response = $this->tmdbService->getPopularMovies(1);
                
                if (!is_array($response)) {
                    throw new \Exception('TMDB API response should be an array');
                }
                if (!isset($response['results'])) {
                    throw new \Exception('TMDB API response should contain results');
                }
                
                echo "✓ Basic TMDB API connection successful\n";
                echo "  - Retrieved " . count($response['results']) . " movies\n";
                echo "  - Total results: " . $response['total_results'] . "\n";
            }
            
            public function testPopularMovies() {
                $response = $this->tmdbService->getPopularMovies(1);
                
                if (!is_array($response)) {
                    throw new \Exception('Popular movies response should be an array');
                }
                if (!isset($response['results'])) {
                    throw new \Exception('Popular movies response should contain results');
                }
                if (count($response['results']) === 0) {
                    throw new \Exception('Should return at least one movie');
                }
                
                $firstMovie = $response['results'][0];
                if (!isset($firstMovie['id']) || !isset($firstMovie['title'])) {
                    throw new \Exception('Movie should have ID and title');
                }
                
                echo "✓ Popular movies endpoint successful\n";
                echo "  - Retrieved " . count($response['results']) . " movies\n";
                echo "  - Total results: " . $response['total_results'] . "\n";
                echo "  - Sample movie: " . $firstMovie['title'] . " (ID: " . $firstMovie['id'] . ")\n";
            }
            
            public function testSearchMovies() {
                $response = $this->tmdbService->searchMovies('Batman', 1);
                
                if (!is_array($response)) {
                    throw new \Exception('Search movies response should be an array');
                }
                if (!isset($response['results'])) {
                    throw new \Exception('Search response should contain results');
                }
                if (count($response['results']) === 0) {
                    throw new \Exception('Search should return at least one result');
                }
                
                echo "✓ Search movies endpoint successful\n";
                echo "  - Search term: 'Batman'\n";
                echo "  - Results found: " . count($response['results']) . "\n";
                echo "  - Total results: " . $response['total_results'] . "\n";
            }
            
            public function testMovieGenres() {
                $response = $this->tmdbService->getMovieGenres();
                
                if (!is_array($response)) {
                    throw new \Exception('Movie genres response should be an array');
                }
                if (count($response) === 0) {
                    throw new \Exception('Should return at least one genre');
                }
                
                $firstGenre = $response[0];
                if (!isset($firstGenre['id']) || !isset($firstGenre['name'])) {
                    throw new \Exception('Genre should have ID and name');
                }
                
                echo "✓ Movie genres endpoint successful\n";
                echo "  - Genres retrieved: " . count($response) . "\n";
                echo "  - Sample genres: " . $response[0]['name'] . ", " . $response[1]['name'] . ", " . $response[2]['name'] . "\n";
            }
            
            public function testTvSeries() {
                $popularTv = $this->tmdbService->getPopularTv(1);
                
                if (!is_array($popularTv)) {
                    throw new \Exception('Popular TV response should be an array');
                }
                if (!isset($popularTv['results'])) {
                    throw new \Exception('Popular TV response should contain results');
                }
                if (count($popularTv['results']) === 0) {
                    throw new \Exception('Should return at least one TV series');
                }
                
                $tvId = $popularTv['results'][0]['id'];
                $tvDetails = $this->tmdbService->getTvDetails($tvId);
                
                if (!isset($tvDetails['name']) || !isset($tvDetails['first_air_date'])) {
                    throw new \Exception('TV details should contain name and first_air_date');
                }
                
                echo "✓ TV series endpoints successful\n";
                echo "  - Popular TV series: " . count($popularTv['results']) . " retrieved\n";
                echo "  - Sample TV series: " . $tvDetails['name'] . " (Seasons: " . $tvDetails['number_of_seasons'] . ")\n";
            }
            
            public function testImageUrlGeneration() {
                $posterPath = '/kqjL17yufvn9OVLyXYpvtyrFfak.jpg';
                $posterUrl = $this->tmdbService->getImageUrl($posterPath, 'w500');
                
                if (!str_contains($posterUrl, 'image.tmdb.org')) {
                    throw new \Exception('Poster URL should contain TMDB image domain');
                }
                
                $backdropPath = '/xJHokMbljvjADYdit5fK5VQsXEG.jpg';
                $backdropUrl = $this->tmdbService->getBackdropUrl($backdropPath, 'original');
                
                if (!str_contains($backdropUrl, 'image.tmdb.org')) {
                    throw new \Exception('Backdrop URL should contain TMDB image domain');
                }
                
                echo "✓ Image URL generation successful\n";
                echo "  - Poster URL: " . $posterUrl . "\n";
                echo "  - Backdrop URL: " . $backdropUrl . "\n";
            }
            
            public function runComprehensiveTest() {
                echo "=== TMDB API Comprehensive Test ===\n\n";
                
                $tests = [
                    'testApiKeyConfiguration',
                    'testBasicConnection', 
                    'testPopularMovies',
                    'testSearchMovies',
                    'testMovieGenres',
                    'testTvSeries',
                    'testImageUrlGeneration'
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
        };
        
        $testRunner = new $testRunner($tmdbService);
        
        try {
            if ($this->option('quick')) {
                $this->info('Running quick tests (basic connection only)...');
                $this->line('');
                
                // Run only basic tests
                $testRunner->testApiKeyConfiguration();
                $this->line('');
                $testRunner->testBasicConnection();
                
                $this->info('✅ Quick TMDB API test completed successfully!');
            } else {
                $this->info('Running comprehensive TMDB API tests...');
                $this->line('');
                
                // Run comprehensive test
                $success = $testRunner->runComprehensiveTest();
                
                if ($success) {
                    $this->info('✅ All TMDB API tests completed successfully!');
                } else {
                    $this->error('❌ Some TMDB API tests failed. Please check the output above.');
                    return 1;
                }
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed with error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}

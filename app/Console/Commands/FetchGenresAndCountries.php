<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Services\TmdbService;
use App\Models\Genre;
use App\Models\Country;

class FetchGenresAndCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-genres-and-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch genres and countries from TMDB API and save to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tmdbService = new TmdbService();
        
        $this->info('Fetching genres from TMDB...');
        $this->fetchGenres($tmdbService);
        
        $this->info('Fetching countries from TMDB...');
        $this->fetchCountries($tmdbService);
        
        $this->info('Genres and countries have been successfully fetched and saved!');
    }

    /**
     * Fetch genres from TMDB and save to database
     */
    private function fetchGenres($tmdbService)
    {
        try {
            $genres = $tmdbService->getGenres();
            
            foreach ($genres as $genre) {
                Genre::updateOrCreate(
                    ['tmdb_id' => $genre['id']],
                    [
                        'name' => $genre['name'],
                        'slug' => Str::slug($genre['name'])
                    ]
                );
            }
            
            $this->info(count($genres) . ' genres have been saved to database.');
        } catch (\Exception $e) {
            $this->error('Error fetching genres: ' . $e->getMessage());
        }
    }

    /**
     * Fetch countries from TMDB and save to database
     */
    private function fetchCountries($tmdbService)
    {
        try {
            $countries = $tmdbService->getCountries();
            
            foreach ($countries as $country) {
                Country::updateOrCreate(
                    ['iso_3166_1' => $country['iso_3166_1']],
                    [
                        'english_name' => $country['english_name'] ?? $country['name'] ?? $country['iso_3166_1'],
                        'slug' => Str::slug($country['english_name'] ?? $country['name'] ?? $country['iso_3166_1'])
                    ]
                );
            }
            
            $this->info(count($countries) . ' countries have been saved to database.');
        } catch (\Exception $e) {
            $this->error('Error fetching countries: ' . $e->getMessage());
        }
    }
}

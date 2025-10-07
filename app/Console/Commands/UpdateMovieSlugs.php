<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateMovieSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:update-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update slugs for all movies that don\'t have a slug';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update movie slugs...');

        $moviesWithoutSlug = Movie::whereNull('slug')->orWhere('slug', '')->get();
        $count = $moviesWithoutSlug->count();

        if ($count === 0) {
            $this->info('All movies already have slugs!');
            return 0;
        }

        $this->info("Found {$count} movies without slugs. Updating...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($moviesWithoutSlug as $movie) {
            $slug = $this->generateUniqueSlug($movie->title);
            $movie->slug = $slug;
            $movie->save();
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully updated slugs for {$count} movies!");

        return 0;
    }

    /**
     * Generate a unique slug from the given title.
     *
     * @param  string  $title
     * @return string
     */
    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = Movie::where('slug', 'LIKE', "{$slug}%")->count();
        
        return $count > 0 ? "{$slug}-" . ($count + 1) : $slug;
    }
}

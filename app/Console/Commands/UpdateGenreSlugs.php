<?php

namespace App\Console\Commands;

use App\Models\Genre;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateGenreSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-genre-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update slugs for existing genres that have null or empty slugs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating genre slugs...');

        $genres = Genre::whereNull('slug')->orWhere('slug', '')->get();
        $updatedCount = 0;
        $skippedCount = 0;

        if ($genres->isEmpty()) {
            $this->info('No genres found that need slug updates.');
            return 0;
        }

        $this->info("Found {$genres->count()} genres that need slug updates.");

        foreach ($genres as $genre) {
            try {
                $originalSlug = Str::slug($genre->name);
                $slug = $originalSlug;
                $count = 1;

                // Check if slug already exists and make it unique
                while (Genre::where('slug', $slug)->where('id', '!=', $genre->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }

                $genre->slug = $slug;
                $genre->save();

                $this->line("Updated genre '{$genre->name}' with slug: {$slug}");
                $updatedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to update genre '{$genre->name}': {$e->getMessage()}");
                $skippedCount++;
            }
        }

        $this->info("Slug update completed!");
        $this->info("Updated: {$updatedCount} genres");
        $this->info("Skipped: {$skippedCount} genres");

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateTvSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tv:update-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update slugs for TV series that are missing them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating TV series slugs...');

        // Find all TV series without slugs
        $tvs = Tv::whereNull('slug')->orWhere('slug', '')->get();

        if ($tvs->isEmpty()) {
            $this->info('All TV series already have slugs.');
            return 0;
        }

        $this->info("Found {$tvs->count()} TV series without slugs.");

        foreach ($tvs as $tv) {
            // Generate slug from title
            $slug = Str::slug($tv->title);
            $originalSlug = $slug;
            $counter = 1;
            
            // Ensure slug is unique
            while (Tv::where('slug', $slug)->where('id', '!=', $tv->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $tv->slug = $slug;
            $tv->save();
            
            $this->info("Updated slug for TV series '{$tv->title}' to '{$slug}'");
        }

        $this->info('TV series slugs updated successfully!');
        return 0;
    }
}

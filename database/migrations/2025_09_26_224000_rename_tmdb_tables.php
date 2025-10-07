<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename tmdb_genres to genres
        if (Schema::hasTable('tmdb_genres')) {
            Schema::rename('tmdb_genres', 'genres');
        }
        
        // Rename tmdb_countries to countries
        if (Schema::hasTable('tmdb_countries')) {
            Schema::rename('tmdb_countries', 'countries');
        }
        
        // Rename tmdb_actors to actors
        if (Schema::hasTable('tmdb_actors')) {
            Schema::rename('tmdb_actors', 'actors');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the renaming
        if (Schema::hasTable('genres')) {
            Schema::rename('genres', 'tmdb_genres');
        }
        
        if (Schema::hasTable('countries')) {
            Schema::rename('countries', 'tmdb_countries');
        }
        
        if (Schema::hasTable('actors')) {
            Schema::rename('actors', 'tmdb_actors');
        }
    }
};

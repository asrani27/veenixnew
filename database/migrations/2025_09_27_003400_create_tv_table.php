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
        Schema::create('tv', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tmdb_id')->nullable()->unique();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->text('overview')->nullable();
            $table->text('description')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->date('first_air_date')->nullable();
            $table->date('last_air_date')->nullable();
            $table->integer('episode_run_time')->nullable();
            $table->integer('number_of_seasons')->default(0);
            $table->integer('number_of_episodes')->default(0);
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('vote_count')->default(0);
            $table->decimal('popularity', 8, 2)->default(0);
            $table->string('original_language', 10)->default('en');
            $table->string('status')->default('Released');
            $table->string('tagline')->nullable();
            $table->string('type')->default('Scripted');
            $table->json('genres')->nullable();
            $table->string('homepage')->nullable();
            $table->boolean('in_production')->default(false);
            $table->string('local_poster_path')->nullable();
            $table->string('local_backdrop_path')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('tmdb_id');
            $table->index('first_air_date');
            $table->index('last_air_date');
            $table->index('vote_average');
            $table->index('popularity');
            $table->index('status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv');
    }
};

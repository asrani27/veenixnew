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
        // Seasons table
        Schema::create('seasons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('tv_id', 36);
            $table->integer('tmdb_id')->nullable();
            $table->integer('season_number');
            $table->string('name');
            $table->text('overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->date('air_date')->nullable();
            $table->integer('episode_count')->default(0);
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('vote_count')->default(0);
            $table->string('local_poster_path')->nullable();
            $table->timestamps();
            
            $table->foreign('tv_id')->references('id')->on('tv')->onDelete('cascade');
            $table->unique(['tv_id', 'season_number']);
            $table->index('tmdb_id');
        });

        // Episodes table
        Schema::create('episodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('tv_id', 36);
            $table->char('season_id', 36);
            $table->integer('tmdb_id')->nullable();
            $table->integer('season_number');
            $table->integer('episode_number');
            $table->string('name');
            $table->text('overview')->nullable();
            $table->string('still_path')->nullable();
            $table->date('air_date')->nullable();
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('vote_count')->default(0);
            $table->integer('runtime')->nullable();
            $table->string('local_still_path')->nullable();
            $table->timestamps();
            
            $table->foreign('tv_id')->references('id')->on('tv')->onDelete('cascade');
            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');
            $table->unique(['tv_id', 'season_number', 'episode_number']);
            $table->index('tmdb_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
        Schema::dropIfExists('seasons');
    }
};

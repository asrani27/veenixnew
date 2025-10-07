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
        // TV Series Genres pivot table
        Schema::create('tv_genre', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->char('tv_id', 36)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->char('genre_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->timestamps();
            
            $table->primary(['tv_id', 'genre_id']);
            $table->foreign('tv_id')->references('id')->on('tv')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
        });

        // TV Series Countries pivot table
        Schema::create('tv_country', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->char('tv_id', 36)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->char('country_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->timestamps();
            
            $table->primary(['tv_id', 'country_id']);
            $table->foreign('tv_id')->references('id')->on('tv')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });

        // TV Series Actors pivot table
        Schema::create('tv_actor', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            
            $table->char('tv_id', 36)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->char('actor_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->string('character')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->integer('order')->default(0);
            $table->string('profile_path')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->timestamps();
            
            $table->primary(['tv_id', 'actor_id']);
            $table->foreign('tv_id')->references('id')->on('tv')->onDelete('cascade');
            $table->foreign('actor_id')->references('id')->on('actors')->onDelete('cascade');
            
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_actor');
        Schema::dropIfExists('tv_country');
        Schema::dropIfExists('tv_genre');
    }
};

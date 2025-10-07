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
        // Movie-Genre relationship table
        Schema::create('movie_genre', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_general_ci';
            
            $table->char('movie_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->char('genre_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->timestamps();
            
            $table->primary(['movie_id', 'genre_id']);
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
        });

        // Movie-Country relationship table
        Schema::create('movie_country', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_general_ci';
            
            $table->char('movie_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->char('country_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->timestamps();
            
            $table->primary(['movie_id', 'country_id']);
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });

        // Movie-Actor relationship table
        Schema::create('movie_actor', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_general_ci';
            
            $table->char('movie_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->char('actor_id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->string('character')->nullable();
            $table->integer('order')->default(0);
            $table->string('profile_path')->nullable();
            $table->timestamps();
            
            $table->primary(['movie_id', 'actor_id']);
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');
            $table->foreign('actor_id')->references('id')->on('actors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_actor');
        Schema::dropIfExists('movie_country');
        Schema::dropIfExists('movie_genre');
    }
};

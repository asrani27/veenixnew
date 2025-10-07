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
        Schema::table('movie', function (Blueprint $table) {
            $table->string('tmdb_id')->nullable()->unique()->after('id');
            $table->string('original_title')->nullable()->after('title');
            $table->text('overview')->nullable()->after('description');
            $table->string('poster_path')->nullable()->after('overview');
            $table->string('backdrop_path')->nullable()->after('poster_path');
            $table->date('release_date')->nullable()->after('backdrop_path');
            $table->integer('runtime')->nullable()->after('release_date');
            $table->decimal('vote_average', 3, 1)->default(0)->after('runtime');
            $table->integer('vote_count')->default(0)->after('vote_average');
            $table->decimal('popularity', 8, 2)->default(0)->after('vote_count');
            $table->boolean('adult')->default(false)->after('popularity');
            $table->string('original_language', 10)->default('en')->after('adult');
            $table->json('genre_ids')->nullable()->after('original_language');
            $table->string('status')->default('Released')->after('genre_ids');
            $table->string('tagline')->nullable()->after('status');
            
            // Add indexes for better performance
            $table->index('tmdb_id');
            $table->index('release_date');
            $table->index('vote_average');
            $table->index('popularity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie', function (Blueprint $table) {
            $table->dropIndex(['tmdb_id']);
            $table->dropIndex(['release_date']);
            $table->dropIndex(['vote_average']);
            $table->dropIndex(['popularity']);
            
            $table->dropColumn([
                'tmdb_id',
                'original_title',
                'overview',
                'poster_path',
                'backdrop_path',
                'release_date',
                'runtime',
                'vote_average',
                'vote_count',
                'popularity',
                'adult',
                'original_language',
                'genre_ids',
                'status',
                'tagline'
            ]);
        });
    }
};

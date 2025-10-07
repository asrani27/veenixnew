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
        Schema::table('reports', function (Blueprint $table) {
            // Drop the existing movie_id column
            $table->dropColumn('movie_id');
        });

        Schema::table('reports', function (Blueprint $table) {
            // Add the movie_id column as a UUID
            $table->uuid('movie_id')->after('id');
            
            // Add foreign key constraint
            $table->foreign('movie_id')->references('id')->on('movie')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['movie_id']);
            
            // Drop the UUID column
            $table->dropColumn('movie_id');
        });

        Schema::table('reports', function (Blueprint $table) {
            // Add back the original unsignedBigInteger column
            $table->unsignedBigInteger('movie_id')->after('id');
        });
    }
};

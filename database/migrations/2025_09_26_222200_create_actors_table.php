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
        Schema::create('actors', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_general_ci';
            
            $table->char('id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci')->primary();
            $table->string('tmdb_id')->charset('utf8mb3')->collation('utf8mb3_general_ci')->unique();
            $table->string('name')->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->string('profile_path')->charset('utf8mb3')->collation('utf8mb3_general_ci')->nullable();
            $table->enum('gender', [0, 1, 2, 3])->default(0)->comment('0: Not specified, 1: Female, 2: Male, 3: Non-binary');
            $table->date('birthday')->nullable();
            $table->date('deathday')->nullable();
            $table->string('place_of_birth')->charset('utf8mb3')->collation('utf8mb3_general_ci')->nullable();
            $table->text('biography')->charset('utf8mb3')->collation('utf8mb3_general_ci')->nullable();
            $table->decimal('popularity', 8, 2)->default(0);
            $table->timestamps();
            
            $table->index('tmdb_id');
            $table->index('name');
            $table->index('popularity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actors');
    }
};

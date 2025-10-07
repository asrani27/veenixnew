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
        Schema::create('genres', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_general_ci';
            
            $table->char('id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci')->primary();
            $table->string('tmdb_id')->charset('utf8mb3')->collation('utf8mb3_general_ci')->unique();
            $table->string('name')->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->timestamps();
            
            $table->index('tmdb_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};

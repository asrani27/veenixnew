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
        Schema::create('countries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_general_ci';
            
            $table->char('id', 36)->charset('utf8mb3')->collation('utf8mb3_general_ci')->primary();
            $table->string('iso_3166_1', 2)->charset('utf8mb3')->collation('utf8mb3_general_ci')->unique();
            $table->string('english_name')->charset('utf8mb3')->collation('utf8mb3_general_ci');
            $table->string('native_name')->charset('utf8mb3')->collation('utf8mb3_general_ci')->nullable();
            $table->timestamps();
            
            $table->index('iso_3166_1');
            $table->index('english_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};

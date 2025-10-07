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
        Schema::create('episode_download_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('episode_id');
            $table->string('url');
            $table->enum('quality', ['540p', '720p']);
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('episode_id')->references('id')->on('episodes')->onDelete('cascade');
            $table->index(['episode_id', 'quality']);
            $table->index(['episode_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episode_download_links');
    }
};

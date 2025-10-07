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
            $table->string('hls_status')->default('pending')->after('slug');
            $table->text('hls_master_playlist_url')->nullable()->after('hls_status');
            $table->text('hls_master_playlist_path')->nullable()->after('hls_master_playlist_url');
            $table->json('hls_files')->nullable()->after('hls_master_playlist_path');
            $table->timestamp('hls_processed_at')->nullable()->after('hls_files');
            $table->text('hls_error')->nullable()->after('hls_processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie', function (Blueprint $table) {
            $table->dropColumn([
                'hls_status',
                'hls_master_playlist_url',
                'hls_master_playlist_path',
                'hls_files',
                'hls_processed_at',
                'hls_error'
            ]);
        });
    }
};

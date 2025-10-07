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
        Schema::table('episodes', function (Blueprint $table) {
            $table->string('hls_status')->default('pending')->after('file');
            $table->text('master_playlist_path')->nullable()->after('hls_status');
            $table->text('hls_playlist_path')->nullable()->after('master_playlist_path');
            $table->text('hls_segment_paths')->nullable()->after('hls_playlist_path');
            $table->integer('hls_progress')->default(0)->after('hls_segment_paths');
            $table->text('hls_error_message')->nullable()->after('hls_progress');
            $table->timestamp('hls_processed_at')->nullable()->after('hls_error_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->dropColumn([
                'hls_status',
                'master_playlist_path',
                'hls_playlist_path',
                'hls_segment_paths',
                'hls_progress',
                'hls_error_message',
                'hls_processed_at'
            ]);
        });
    }
};

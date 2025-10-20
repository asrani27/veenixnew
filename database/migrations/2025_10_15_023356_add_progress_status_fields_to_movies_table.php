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
        Schema::table('movies', function (Blueprint $table) {
            $table->string('status_upload_to_local')->default('waiting');
            $table->string('status_progressive')->default('waiting');
            $table->string('status_upload_to_wasabi')->default('waiting');

            $table->integer('progress_upload_to_local')->default(0);
            $table->integer('progress_progressive')->default(0);
            $table->integer('progress_upload_to_wasabi')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn([
                'status_upload_to_local',
                'status_progressive',
                'status_upload_to_wasabi',
                'progress_upload_to_local',
                'progress_progressive',
                'progress_upload_to_wasabi'
            ]);
        });
    }
};

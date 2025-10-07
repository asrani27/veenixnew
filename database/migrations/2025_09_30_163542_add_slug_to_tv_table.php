<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if slug column already exists
        if (!Schema::hasColumn('tv', 'slug')) {
            // Add the column without unique constraint
            Schema::table('tv', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('title');
            });
        }

        // Generate slugs for existing records that don't have slugs
        $tvs = DB::table('tv')->whereNull('slug')->orWhere('slug', '')->get();
        foreach ($tvs as $tv) {
            $slug = Str::slug($tv->title);
            $originalSlug = $slug;
            $counter = 1;
            
            // Ensure slug is unique
            while (DB::table('tv')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            DB::table('tv')->where('id', $tv->id)->update(['slug' => $slug]);
        }

        // Check if unique constraint exists, if not add it
        $indexes = DB::select("SHOW INDEX FROM tv WHERE Column_name = 'slug'");
        $hasUniqueConstraint = false;
        
        foreach ($indexes as $index) {
            if ($index->Key_name === 'tv_slug_unique' || $index->Non_unique === 0) {
                $hasUniqueConstraint = true;
                break;
            }
        }
        
        if (!$hasUniqueConstraint) {
            Schema::table('tv', function (Blueprint $table) {
                $table->string('slug')->nullable(false)->change();
                $table->unique('slug', 'tv_slug_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tv', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

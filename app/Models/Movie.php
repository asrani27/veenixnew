<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Movie extends Model
{

    use HasUuids;
    protected $table = 'movie';
    protected $guarded = ['id'];
    protected $casts = [
        'hls_files' => 'array',
        'hls_processed_at' => 'datetime',
    ];

    /**
     * The genres that belong to the movie.
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    /**
     * The countries that belong to the movie.
     */
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'movie_country');
    }

    /**
     * The actors that belong to the movie.
     */
    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'movie_actor')
            ->withPivot('character', 'order', 'profile_path')
            ->withTimestamps()
            ->orderBy('movie_actor.order', 'asc');
    }

    /**
     * Get the poster URL attribute
     */
    public function getPosterUrlAttribute()
    {
        if ($this->local_poster_path) {
            return Storage::url($this->local_poster_path);
        }

        if ($this->poster_path) {
            return 'https://image.tmdb.org/t/p/w500' . $this->poster_path;
        }

        return 'https://via.placeholder.com/300x450?text=No+Image';
    }

    /**
     * Get the backdrop URL attribute
     */
    public function getBackdropUrlAttribute()
    {
        if ($this->local_backdrop_path) {
            return Storage::url($this->local_backdrop_path);
        }

        if ($this->backdrop_path) {
            return 'https://image.tmdb.org/t/p/original' . $this->backdrop_path;
        }

        return 'https://via.placeholder.com/800x400?text=No+Backdrop';
    }

    /**
     * Get the genres array attribute
     */
    public function getGenresArrayAttribute()
    {
        if ($this->genre_ids) {
            return json_decode($this->genre_ids, true) ?? [];
        }
        return [];
    }

    /**
     * Scope a query to only include featured movies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->whereNotNull('poster_path')
            ->where('title', '!=', '')
            ->orderBy('created_at', 'desc')
            ->take(8); // Limit to 8 for the homepage
    }

    /**
     * Scope a query to get most watched movies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMostWatched($query)
    {
        return $query->whereNotNull('poster_path')
            ->where('title', '!=', '')
            ->orderBy('views', 'desc')
            ->take(10); // Limit to 10 for the sidebar
    }

    /**
     * Scope a query to get movies by country name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $countryName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCountry($query, $countryName)
    {
        return $query->whereHas('countries', function($q) use ($countryName) {
            $q->where('english_name', $countryName);
        });
    }

    /**
     * Scope a query to get movies for homepage display.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForHomepage($query)
    {
        return $query->whereNotNull('poster_path')
            ->where('title', '!=', '')
            ->orderBy('created_at', 'desc')
            ->take(8);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movie) {
            if (empty($movie->slug)) {
                $movie->slug = static::generateUniqueSlug($movie->title);
            }
        });

        static::updating(function ($movie) {
            if ($movie->isDirty('title') && empty($movie->slug)) {
                $movie->slug = static::generateUniqueSlug($movie->title);
            }
        });
    }

    /**
     * Generate a unique slug from the given title.
     *
     * @param  string  $title
     * @return string
     */
    private static function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'LIKE', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-" . ($count + 1) : $slug;
    }

    /**
     * Check if the movie has HLS video ready
     *
     * @return bool
     */
    public function hasHlsVideo()
    {
        return $this->hls_status === 'completed' && !empty($this->hls_master_playlist_url);
    }

    /**
     * Get HLS master playlist URL
     *
     * @return string|null
     */
    public function getHlsUrl()
    {
        return $this->hls_master_playlist_url;
    }

    /**
     * Check if HLS conversion is in progress
     *
     * @return bool
     */
    public function isHlsProcessing()
    {
        return $this->hls_status === 'pending' || $this->hls_status === 'processing';
    }

    /**
     * Check if HLS conversion failed
     *
     * @return bool
     */
    public function hasHlsError()
    {
        return $this->hls_status === 'failed';
    }

    /**
     * Get HLS status with human readable format
     *
     * @return string
     */
    public function getHlsStatusAttribute($value)
    {
        return $value ?? 'pending';
    }

    /**
     * Get HLS files count
     *
     * @return int
     */
    public function getHlsFilesCount()
    {
        $files = $this->hls_files ? json_decode($this->hls_files, true) : [];
        return count($files);
    }

    /**
     * Get HLS segment files
     *
     * @return array
     */
    public function getHlsSegments()
    {
        return array_filter($this->hls_files ?? [], function($file) {
            return ($file['type'] ?? null) === 'segment';
        });
    }

    /**
     * Get HLS playlist files
     *
     * @return array
     */
    public function getHlsPlaylists()
    {
        return array_filter($this->hls_files ?? [], function($file) {
            return ($file['type'] ?? null) === 'playlist';
        });
    }

    /**
     * Scope a query to only include movies with HLS video
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithHls($query)
    {
        return $query->where('hls_status', 'completed')
                    ->whereNotNull('hls_master_playlist_url');
    }

    /**
     * Scope a query to only include movies processing HLS
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessingHls($query)
    {
        return $query->whereIn('hls_status', ['pending', 'processing']);
    }

    /**
     * Initialize HLS conversion
     *
     * @return void
     */
    public function initializeHlsConversion()
    {
        $this->update([
            'hls_status' => 'pending',
            'hls_error' => null,
            'hls_processed_at' => null
        ]);
    }
}

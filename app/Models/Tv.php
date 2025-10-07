<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tv extends Model
{
    use HasUuids;

    protected $table = 'tv';
    protected $guarded = ['id'];

    protected $fillable = [
        'tmdb_id',
        'title',
        'slug',
        'original_title',
        'overview',
        'description',
        'poster_path',
        'backdrop_path',
        'first_air_date',
        'last_air_date',
        'episode_run_time',
        'number_of_seasons',
        'number_of_episodes',
        'vote_average',
        'vote_count',
        'popularity',
        'original_language',
        'status',
        'tagline',
        'type',
        'genre_data',
        'homepage',
        'in_production',
        'local_poster_path',
        'local_backdrop_path',
    ];

    protected $casts = [
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'episode_run_time' => 'integer',
        'number_of_seasons' => 'integer',
        'number_of_episodes' => 'integer',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'popularity' => 'float',
        'in_production' => 'boolean',
        'genre_data' => 'array',
    ];

    /**
     * Get the genres associated with the TV series.
     */
    public function genres_data(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'tv_genre');
    }

    /**
     * Get the countries associated with the TV series.
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'tv_country');
    }

    /**
     * Get the actors associated with the TV series.
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'tv_actor')
            ->withPivot(['character', 'order', 'profile_path'])
            ->orderBy('pivot_order');
    }

    /**
     * Get the seasons for the TV series.
     */
    public function seasons()
    {
        return $this->hasMany(Season::class)->orderBy('season_number');
    }

    /**
     * Get all episodes for the TV series.
     */
    public function episodes()
    {
        return $this->hasManyThrough(Episode::class, Season::class);
    }

    /**
     * Get the poster URL attribute
     */
    public function getPosterUrlAttribute()
    {
        if ($this->local_poster_path) {
            return asset('storage/' . $this->local_poster_path);
        }
        
        if ($this->poster_path) {
            return 'https://image.tmdb.org/t/p/w500' . $this->poster_path;
        }
        
        return 'https://via.placeholder.com/300x450?text=No+Image';
    }

    /**
     * Scope a query to get TV series for the homepage.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForHomepage($query)
    {
        return $query->whereNotNull('poster_path')
                    ->where('title', '!=', '')
                    ->orderBy('created_at', 'desc')
                    ->take(10); // Taking 10 for the tv series section
    }

    /**
     * Scope a query to get ongoing TV series.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOngoing($query)
    {
        return $query->whereNotNull('poster_path')
                    ->where('title', '!=', '')
                    ->whereIn('status', ['Returning Series', 'In Production', 'Planned'])
                    ->orderBy('popularity', 'desc')
                    ->take(10); // Limit to 10 for the sidebar
    }

    /**
     * Scope a query to get TV series by country name.
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
     * Scope a query to get TV series that have episodes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasEpisodes($query)
    {
        return $query->whereHas('seasons.episodes', function($q) {
            $q->where('publish', true);
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Genre extends Model
{
    use HasUuids;

    protected $table = 'genres';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($genre) {
            if (empty($genre->slug)) {
                $genre->slug = static::generateUniqueSlug($genre->name);
            }
        });

        static::updating(function ($genre) {
            if ($genre->isDirty('name') && empty($genre->slug)) {
                $genre->slug = static::generateUniqueSlug($genre->name);
            }
        });
    }

    /**
     * Generate a unique slug for the genre.
     *
     * @param string $name
     * @return string
     */
    public static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = 1;
        $originalSlug = $slug;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * The movies that belong to the genre.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_genre');
    }

    /**
     * The TV series that belong to the genre.
     */
    public function tvs()
    {
        return $this->belongsToMany(Tv::class, 'tv_genre');
    }
}

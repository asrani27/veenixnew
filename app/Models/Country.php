<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Country extends Model
{
    use HasUuids;
    
    protected $table = 'countries';
    protected $guarded = ['id'];

    /**
     * The movies that belong to the country.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_country');
    }
}

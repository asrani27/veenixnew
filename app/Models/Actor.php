<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Actor extends Model
{
    use HasUuids;
    
    protected $table = 'actors';
    protected $guarded = ['id'];
    
    protected $casts = [
        'gender' => 'string',
        'popularity' => 'decimal:2',
        'birthday' => 'date',
        'deathday' => 'date',
    ];

    /**
     * The movies that belong to the actor.
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_actor')
                    ->withPivot('character', 'order', 'profile_path')
                    ->withTimestamps();
    }
}

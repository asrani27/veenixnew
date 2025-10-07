<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Season extends Model
{
    use HasUuids;
    
    protected $table = 'seasons';
    protected $guarded = ['id'];
    
    protected $fillable = [
        'tv_id',
        'tmdb_id',
        'season_number',
        'name',
        'overview',
        'poster_path',
        'air_date',
        'episode_count',
        'vote_average',
        'vote_count',
        'local_poster_path',
    ];
    
    protected $casts = [
        'air_date' => 'date',
        'episode_count' => 'integer',
        'vote_average' => 'float',
        'vote_count' => 'integer',
    ];
    
    public function tv()
    {
        return $this->belongsTo(Tv::class);
    }
    
    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}

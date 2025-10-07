<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Episode extends Model
{
    use HasUuids;
    
    protected $table = 'episodes';
    protected $guarded = ['id'];
    
    protected $fillable = [
        'tv_id',
        'season_id',
        'tmdb_id',
        'season_number',
        'episode_number',
        'name',
        'overview',
        'still_path',
        'air_date',
        'vote_average',
        'vote_count',
        'runtime',
        'local_still_path',
        'file',
        'hls_status',
        'master_playlist_path',
        'hls_playlist_path',
        'hls_segment_paths',
        'hls_progress',
        'hls_error_message',
        'hls_processed_at',
        'views',
        'publish',
    ];
    
    protected $casts = [
        'air_date' => 'date',
        'vote_average' => 'float',
        'vote_count' => 'integer',
        'runtime' => 'integer',
        'hls_progress' => 'integer',
        'hls_processed_at' => 'datetime',
        'hls_segment_paths' => 'array',
        'publish' => 'boolean',
    ];
    
    public function tv()
    {
        return $this->belongsTo(Tv::class);
    }
    
    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}

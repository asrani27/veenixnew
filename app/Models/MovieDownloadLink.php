<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MovieDownloadLink extends Model
{
    use HasUuids;
    
    protected $table = 'movie_download_links';
    protected $guarded = ['id'];
    
    protected $fillable = [
        'movie_id',
        'url',
        'quality',
        'label',
        'is_active',
        'sort_order',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
    
    /**
     * Scope to get only active download links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'asc');
    }
    
    /**
     * Scope to filter by quality
     */
    public function scopeByQuality($query, $quality)
    {
        return $query->where('quality', $quality);
    }
}

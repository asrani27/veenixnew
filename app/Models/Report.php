<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'movie_id',
        'movie_title',
        'issue_type',
        'description',
        'email',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    public function getIssueTypeLabelAttribute(): string
    {
        return match($this->issue_type) {
            'video_not_playing' => 'Video cannot be played',
            'broken_link' => 'Broken link',
            'poor_quality' => 'Poor video quality',
            'audio_problem' => 'Audio problem',
            'subtitle_issue' => 'Subtitle issue',
            'other' => 'Other',
            default => $this->issue_type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'reviewed' => 'Reviewed',
            'resolved' => 'Resolved',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'resolved' => 'green',
            default => 'gray',
        };
    }
}

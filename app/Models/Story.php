<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    protected $fillable = [
        'user_id',
        'media_path',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active stories.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('expires_at', '>', now());
    }

    /**
     * Get URL of story media.
     */
    public function getMediaUrlAttribute(): string
    {
        return Storage::url($this->media_path);
    }
}

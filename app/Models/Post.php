<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'content',
        'media_path',
        'parent_id',
        'is_archived',
        'repost_of_id',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Post::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /** The original post this is a repost/quote-repost of */
    public function repostOf()
    {
        return $this->belongsTo(Post::class, 'repost_of_id')->with('user');
    }

    /** All reposts of this post */
    public function reposts()
    {
        return $this->hasMany(Post::class, 'repost_of_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    /** Only non-archived posts */
    public function scopeVisible(Builder $query): void
    {
        $query->where('is_archived', false);
    }

    /** Only archived posts */
    public function scopeArchived(Builder $query): void
    {
        $query->where('is_archived', true);
    }

    // ── Accessors ────────────────────────────────────────────────────────

    public function isLikedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isRepostedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->reposts()->where('user_id', $user->id)->exists();
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->media_path ? Storage::url($this->media_path) : null;
    }
}

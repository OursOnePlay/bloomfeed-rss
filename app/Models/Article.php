<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'guid', 'title', 'link', 'excerpt', 'content', 'published_at', 'is_read', 'is_favorite',
    'full_content_markdown', 'full_content_fetched_at', 'full_content_error',
])]
class Article extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_read' => 'boolean',
            'is_favorite' => 'boolean',
            'full_content_fetched_at' => 'datetime',
        ];
    }

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('feed', fn (Builder $q) => $q->where('user_id', $userId));
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }
}

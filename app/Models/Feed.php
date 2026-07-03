<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['url', 'title', 'site_title', 'last_fetched_at', 'last_fetch_status'])]
class Feed extends Model
{
    protected function casts(): array
    {
        return [
            'last_fetched_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function displayTitle(): string
    {
        return $this->title ?: ($this->site_title ?: $this->url);
    }
}

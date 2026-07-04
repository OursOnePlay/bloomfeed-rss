<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['type', 'name', 'url', 'scope', 'enabled', 'last_triggered_at', 'last_error'])]
class Notifier extends Model
{
    public const TYPE_DISCORD = 'discord';

    public const TYPE_WEBHOOK = 'webhook';

    public const SCOPE_ALL = 'all';

    public const SCOPE_FEEDS = 'feeds';

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'last_triggered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feeds(): BelongsToMany
    {
        return $this->belongsToMany(Feed::class);
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function scopeForFeed(Builder $query, int $feedId): Builder
    {
        return $query->where(function (Builder $q) use ($feedId) {
            $q->where('scope', self::SCOPE_ALL)
                ->orWhere(function (Builder $q2) use ($feedId) {
                    $q2->where('scope', self::SCOPE_FEEDS)
                        ->whereHas('feeds', fn (Builder $f) => $f->where('feeds.id', $feedId));
                });
        });
    }
}

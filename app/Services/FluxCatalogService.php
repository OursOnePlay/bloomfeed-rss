<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class FluxCatalogService
{
    private const CACHE_KEY = 'bloomflux.catalog';

    private const CACHE_TIME_KEY = 'bloomflux.catalog_updated_at';

    /**
     * Retourne le catalogue BloomFluxDB, en le rafraîchissant depuis le serveur
     * distant quand il est joignable, sinon en servant la dernière copie en cache.
     *
     * @return array{feeds: list<array{title: string, url: string, category: string, lang: string, description: ?string}>, fromCache: bool, updatedAt: ?Carbon}
     */
    public function getCatalog(): array
    {
        try {
            $rows = DB::connection('bloomflux')
                ->table('feeds')
                ->where('is_active', 1)
                ->orderBy('category')
                ->orderBy('title')
                ->get(['title', 'url', 'category', 'lang', 'description'])
                ->map(fn ($row) => (array) $row)
                ->all();

            Cache::forever(self::CACHE_KEY, $rows);
            Cache::forever(self::CACHE_TIME_KEY, now()->toIso8601String());

            return ['feeds' => $rows, 'fromCache' => false, 'updatedAt' => now()];
        } catch (Throwable $e) {
            Log::warning('BloomFluxDB unreachable, serving cached catalog', [
                'error' => $e->getMessage(),
            ]);

            $cached = Cache::get(self::CACHE_KEY, []);
            $updatedAt = Cache::get(self::CACHE_TIME_KEY);

            return [
                'feeds' => $cached,
                'fromCache' => true,
                'updatedAt' => $updatedAt ? Carbon::parse($updatedAt) : null,
            ];
        }
    }
}

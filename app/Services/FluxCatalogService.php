<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FluxCatalogService
{
    /**
     * Retourne le catalogue complet : le fichier embarqué (resources/data/bloomflux.json)
     * fusionné avec les sources externes configurées (URLs de fichiers bloomflux.json
     * hébergés dans d'autres dépôts). Dédoublonné par URL de flux, l'embarqué gagne.
     *
     * @return list<array{title: string, url: string, category: string, lang: string, description: ?string}>
     */
    public function getCatalog(): array
    {
        $feeds = $this->loadEmbedded();

        foreach (Setting::catalogSources() as $sourceUrl) {
            $feeds = array_merge($feeds, Cache::remember('bloomflux.fresh.'.md5($sourceUrl), 300, fn () => $this->loadRemoteSource($sourceUrl)));
        }

        $seen = [];
        $unique = [];

        foreach ($feeds as $feed) {
            if (! isset($seen[$feed['url']])) {
                $seen[$feed['url']] = true;
                $unique[] = $feed;
            }
        }

        return $unique;
    }

    /**
     * Valide une source distante et retourne le nombre de flux qu'elle contient,
     * ou null si elle est injoignable ou invalide.
     */
    public function validateSource(string $sourceUrl): ?int
    {
        $feeds = $this->fetchSource($sourceUrl);

        return $feeds === null ? null : count($feeds);
    }

    private function loadEmbedded(): array
    {
        try {
            $raw = file_get_contents(resource_path('data/bloomflux.json'));
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

            return $this->normalize($data['feeds'] ?? []);
        } catch (Throwable $e) {
            Log::warning('Catalogue bloomflux.json illisible', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Charge une source externe : réseau si joignable (et met le cache à jour),
     * sinon dernière copie en cache.
     */
    private function loadRemoteSource(string $sourceUrl): array
    {
        $cacheKey = 'bloomflux.source.'.md5($sourceUrl);

        $feeds = $this->fetchSource($sourceUrl);

        if ($feeds !== null) {
            Cache::forever($cacheKey, $feeds);

            return $feeds;
        }

        Log::warning('Source de catalogue injoignable, utilisation du cache', ['source' => $sourceUrl]);

        return Cache::get($cacheKey, []);
    }

    private function fetchSource(string $sourceUrl): ?array
    {
        try {
            $response = Http::timeout(6)
                ->withHeaders(['User-Agent' => 'BloomFeed/1.0'])
                ->get($sourceUrl);

            if ($response->failed()) {
                return null;
            }

            $data = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

            if (! isset($data['feeds']) || ! is_array($data['feeds'])) {
                return null;
            }

            return $this->normalize($data['feeds']);
        } catch (Throwable) {
            return null;
        }
    }

    private function normalize(array $feeds): array
    {
        $clean = [];

        foreach ($feeds as $feed) {
            if (! is_array($feed) || empty($feed['title']) || empty($feed['url'])) {
                continue;
            }

            $clean[] = [
                'title' => (string) $feed['title'],
                'url' => (string) $feed['url'],
                'category' => (string) ($feed['category'] ?? 'Divers'),
                'lang' => strtolower((string) ($feed['lang'] ?? 'en')),
                'description' => isset($feed['description']) ? (string) $feed['description'] : null,
            ];
        }

        return $clean;
    }
}

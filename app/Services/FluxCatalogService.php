<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

class FluxCatalogService
{
    /**
     * Retourne le catalogue de flux embarqué (resources/data/bloomflux.json).
     * La liste est versionnée avec le code : la mettre à jour = éditer le fichier et commit.
     *
     * @return list<array{title: string, url: string, category: string, lang: string, description: ?string}>
     */
    public function getCatalog(): array
    {
        try {
            $raw = file_get_contents(resource_path('data/bloomflux.json'));
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

            return $data['feeds'] ?? [];
        } catch (Throwable $e) {
            Log::warning('Catalogue bloomflux.json illisible', ['error' => $e->getMessage()]);

            return [];
        }
    }
}

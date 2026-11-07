<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\FluxCatalogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_is_loaded_in_filtered_pages_instead_of_rendering_every_feed(): void
    {
        $feeds = [];
        for ($i = 0; $i < 60; $i++) {
            $feeds[] = ['title' => 'Source '.$i, 'url' => 'https://example.test/'.$i, 'description' => 'Reading', 'category' => 'Tech', 'lang' => $i < 30 ? 'en' : 'fr'];
        }
        $this->mock(FluxCatalogService::class, fn ($mock) => $mock->shouldReceive('getCatalog')->andReturn($feeds));
        $this->actingAs(User::factory()->create());
        $first = $this->get('/catalog')->assertOk();
        $this->assertSame(24, substr_count($first->getContent(), 'class="market-card"'));
        $next = $this->getJson('/catalog?lang=fr&category=Tech&page=2')->assertOk()->assertJsonPath('total', 30);
        $this->assertSame(6, substr_count($next->json('html'), 'class="market-card"'));
        $this->assertNull($next->json('next'));
        $this->getJson('/catalog?q=no-match')->assertOk()->assertJsonPath('total', 0)->assertJsonPath('html', '');
    }
}

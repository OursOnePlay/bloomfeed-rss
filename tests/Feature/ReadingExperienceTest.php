<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_assets_stay_on_the_browser_origin_without_trusting_forwarded_headers(): void
    {
        config(['app.url' => 'http://localhost', 'security.trusted_proxies' => []]);
        foreach (['/', '/self-hosting', '/login'] as $path) {
            $response = $this->withHeaders(['X-Forwarded-Proto' => 'https'])->get($path);
            $response->assertOk()
                ->assertSee('href="/css/theme.css"', false)
                ->assertSee('href="/css/app.css"', false)
                ->assertSee('src="/js/theme-toggle.js"', false);
            $this->assertDoesNotMatchRegularExpression('/(?:src|href)="http:\/\/[^" ]+\/(?:css|js|images)\//', $response->getContent());
        }
        $this->actingAs(User::factory()->create())->get('/dashboard')
            ->assertOk()->assertSee('href="/css/app.css"', false)
            ->assertSee('src="/js/app.js"', false);
    }

    public function test_english_is_the_default_and_saved_language_is_respected(): void
    {
        $this->get('/login')->assertOk()->assertSee('<html lang="en">', false);
        $this->withSession(['locale' => 'fr'])->get('/login')
            ->assertOk()->assertSee('<html lang="fr">', false);
    }

    public function test_search_and_favorites_stay_scoped_to_the_signed_in_reader(): void
    {
        $reader = User::factory()->create();
        $other = User::factory()->create();
        $feed = $reader->feeds()->create(['url' => 'https://example.test/feed', 'title' => 'Linux journal']);
        $privateFeed = $other->feeds()->create(['url' => 'https://example.test/private', 'title' => 'Linux private']);
        $saved = $feed->articles()->create(['guid' => 'saved', 'title' => 'Linux saved article', 'link' => 'https://example.test/1', 'is_favorite' => true]);
        $feed->articles()->create(['guid' => 'regular', 'title' => 'Linux ordinary article', 'link' => 'https://example.test/2']);
        $privateFeed->articles()->create(['guid' => 'private', 'title' => 'Linux confidential article', 'link' => 'https://example.test/3', 'is_favorite' => true]);

        $this->actingAs($reader)->get('/dashboard?q=Linux&favorite=1')
            ->assertOk()->assertSee($saved->title)
            ->assertDontSee('Linux ordinary article')->assertDontSee('Linux confidential article');
        $this->get('/dashboard?q=journal')->assertOk()->assertSee($saved->title);
        $this->get('/dashboard?q=no-matches')->assertOk()->assertSee(__('design.no_results'));
    }

    public function test_pagination_and_feed_links_keep_reading_filters(): void
    {
        $reader = User::factory()->create();
        $feed = $reader->feeds()->create(['url' => 'https://example.test/feed', 'title' => 'Daily']);
        for ($i = 0; $i < 21; $i++) {
            $feed->articles()->create(['guid' => (string) $i, 'title' => 'Daily article '.$i, 'link' => 'https://example.test/'.$i]);
        }
        $this->actingAs($reader)->get('/dashboard?q=Daily&unread=1')
            ->assertOk()->assertSee('q=Daily&amp;unread=1&amp;page=2', false)
            ->assertSee('q=Daily&amp;unread=1&amp;feed='.$feed->id, false);
    }

    public function test_redesigned_pages_render_and_favorite_can_be_toggled(): void
    {
        $reader = User::factory()->create(['is_owner' => true]);
        $feed = $reader->feeds()->create(['url' => 'https://example.test/feed', 'title' => 'Daily']);
        $article = $feed->articles()->create(['guid' => 'one', 'title' => 'Reading test', 'link' => 'https://example.test/article', 'full_content_markdown' => 'A complete article.']);
        $reader->notifiers()->create(['name' => 'Reading alerts', 'type' => 'webhook', 'url' => 'https://example.test/hook', 'scope' => 'all', 'enabled' => true]);
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->actingAs($reader);
        foreach (['/dashboard', '/catalog', '/feeds', '/notifiers', '/settings', '/articles/'.$article->id, '/self-hosting'] as $path) {
            $this->get($path)->assertOk()->assertSee('id="main-content"', false);
        }
        $this->postJson('/articles/'.$article->id.'/favorite')->assertOk()->assertJson(['is_favorite' => true]);
        $this->post('/articles/'.$article->id.'/favorite')->assertRedirect();
        $this->assertFalse($article->fresh()->is_favorite);
    }
}

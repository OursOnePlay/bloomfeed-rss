<?php

namespace Tests\Feature;

use App\Jobs\RefreshFeed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityControlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_logins_are_limited_and_forwarded_ips_cannot_bypass_the_limit(): void
    {
        Cache::flush();
        User::factory()->create(['email' => 'login@example.test']);
        for ($i = 0; $i < 5; $i++) {
            $this->withHeaders(['X-Forwarded-For' => '198.51.100.'.($i + 1)])
                ->post('/login', ['email' => 'login@example.test', 'password' => 'wrong-password'])->assertStatus(302);
        }
        $this->withHeaders(['X-Forwarded-For' => '198.51.100.99'])
            ->post('/login', ['email' => 'login@example.test', 'password' => 'wrong-password'])->assertStatus(429);
    }

    public function test_password_change_invalidates_stamped_and_legacy_sessions(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);
        $this->actingAs($user)->patch('/account/password', [
            'current_password' => 'old-password', 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertRedirect('/account');
        $this->assertSame(1, (int) $user->fresh()->auth_version);
        $this->get('/account')->assertOk();
        $this->withSession(['auth_version' => 0])->get('/account')->assertRedirect('/login');
        $this->assertGuest();
        $this->actingAs($user->fresh())->withSession(['auth_version' => null])->get('/account')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_bulk_refresh_enqueues_work_without_issuing_http_requests(): void
    {
        Queue::fake();
        Http::fake();
        $user = User::factory()->create();
        $feed = $user->feeds()->create(['url' => 'https://example.com/rss', 'title' => 'Example']);
        $this->actingAs($user)->post('/feeds/refresh-all')->assertRedirect();
        Queue::assertPushed(RefreshFeed::class, fn ($job) => $job->feedId === $feed->id);
        Http::assertNothingSent();
    }

    public function test_subscription_limits_are_enforced_before_work_is_queued(): void
    {
        Queue::fake();
        config(['security.max_feeds' => 0]);
        $this->actingAs(User::factory()->create())->post('/feeds', ['url' => 'https://example.com/rss'])->assertStatus(422);
        Queue::assertNothingPushed();
    }

    public function test_trusted_proxy_and_optional_host_configuration(): void
    {
        Route::get('/network-check', fn (Request $request) => response()->json(['ip' => $request->ip(), 'secure' => $request->isSecure()]));
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.5'])->withHeaders(['X-Forwarded-For' => '198.51.100.10', 'X-Forwarded-Proto' => 'https'])
            ->get('/network-check')->assertJson(['ip' => '192.0.2.5', 'secure' => false]);
        config(['security.trusted_proxies' => ['192.0.2.5']]);
        $this->get('/network-check')->assertJson(['ip' => '198.51.100.10', 'secure' => true]);
        config(['security.trusted_hosts' => ['expected.example']]);
        $this->get('/network-check')->assertStatus(400);
    }
}

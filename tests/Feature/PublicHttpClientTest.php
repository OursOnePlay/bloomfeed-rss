<?php

namespace Tests\Feature;

use App\Services\PublicHttpClient;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class PublicHttpClientTest extends TestCase
{
    #[DataProvider('unsafeDestinations')]
    public function test_unsafe_destinations_are_rejected(string $url): void
    {
        Http::fake();
        $this->expectException(RuntimeException::class);
        try {
            (new PublicHttpClient)->get($url);
        } finally {
            Http::assertNothingSent();
        }
    }

    public static function unsafeDestinations(): array
    {
        return array_map(fn ($url) => [$url], [
            'http://127.0.0.1/private', 'http://10.0.0.1/', 'http://169.254.169.254/', 'http://100.64.0.1/',
            'http://[::1]/', 'http://[::ffff:127.0.0.1]/', 'http://[64:ff9b::a00:1]/', 'http://[ff02::1]/',
            'http://2130706433/', 'http://0177.0.0.1/', 'http://0x7f000001/', 'http://user:pass@example.com/',
            'file:///etc/passwd', 'gopher://example.com/', 'http://example.com:8080/', 'http://localhost/',
            'http://example.com\\@127.0.0.1/',
        ]);
    }

    public function test_public_request_pins_dns_and_disables_redirects_and_proxies(): void
    {
        $client = $this->getMockBuilder(PublicHttpClient::class)->onlyMethods(['resolveAddresses'])->getMock();
        $client->expects($this->once())->method('resolveAddresses')->with('example.com')->willReturn(['93.184.216.34']);
        $options = null;
        Http::fake(function ($request, $requestOptions) use (&$options) {
            $options = $requestOptions;

            return Http::response('OK');
        });
        $this->assertSame('OK', $client->get('https://example.com/')->body());
        $this->assertSame(['example.com:443:93.184.216.34'], $options['curl'][CURLOPT_RESOLVE]);
        $this->assertFalse($options['allow_redirects']);
        $this->assertSame('', $options['proxy']);
        $this->assertFalse($options['decode_content']);
    }

    public function test_redirect_to_private_address_is_blocked_before_the_second_request(): void
    {
        Http::fake(['*' => Http::response('', 302, ['Location' => 'http://127.0.0.1/private'])]);
        $this->expectException(RuntimeException::class);
        try {
            (new PublicHttpClient)->get('https://93.184.216.34/');
        } finally {
            Http::assertSentCount(1);
        }
    }

    public function test_any_private_dns_answer_is_rejected(): void
    {
        $client = $this->getMockBuilder(PublicHttpClient::class)->onlyMethods(['resolveAddresses'])->getMock();
        $client->method('resolveAddresses')->willReturn(['93.184.216.34', '127.0.0.1']);
        Http::fake();
        $this->expectException(RuntimeException::class);
        try {
            $client->get('https://example.com');
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_oversized_response_is_rejected(): void
    {
        Http::fake(['*' => Http::response(str_repeat('x', PublicHttpClient::MAX_BYTES + 1))]);
        $this->expectException(RuntimeException::class);
        (new PublicHttpClient)->get('https://93.184.216.34/');
    }

    public function test_webhook_redirect_does_not_forward_the_payload(): void
    {
        Http::fake(['*' => Http::response('', 307, ['Location' => 'https://1.1.1.1/'])]);
        $this->expectException(RuntimeException::class);
        try {
            (new PublicHttpClient)->post('https://93.184.216.34/', ['private' => 'test']);
        } finally {
            Http::assertSentCount(1);
        }
    }
}

<?php

namespace App\Services;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\IpUtils;

class PublicHttpClient
{
    public const MAX_BYTES = 5 * 1024 * 1024;

    public function get(string $url): Response
    {
        for ($redirects = 0; $redirects <= 3; $redirects++) {
            $response = $this->request('GET', $url);
            if (! in_array($response->status(), [301, 302, 303, 307, 308], true)) {
                return $response;
            }
            $location = $response->header('Location');
            if ($location === '' || $redirects === 3) {
                throw new RuntimeException('Invalid or excessive redirects.');
            }
            $url = (string) UriResolver::resolve(new Uri($url), new Uri($location));
        }
        throw new RuntimeException('Too many redirects.');
    }

    public function post(string $url, array $payload): Response
    {
        $response = $this->request('POST', $url, $payload);
        if ($response->status() >= 300 && $response->status() < 400) {
            throw new RuntimeException('Webhook redirects are not allowed.');
        }

        return $response;
    }

    public function destination(string $url): array
    {
        if (strlen($url) > 2048 || preg_match('/[\x00-\x20\x7f\\\\]/', $url)) {
            throw new RuntimeException('Invalid destination URL.');
        }
        $parts = parse_url($url);
        if (! is_array($parts) || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true)
            || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            throw new RuntimeException('Only public HTTP and HTTPS destinations are allowed.');
        }
        $host = strtolower(trim($parts['host'], '[]'));
        $port = $parts['port'] ?? ($parts['scheme'] === 'https' ? 443 : 80);
        if (! in_array($port, [80, 443], true)) {
            throw new RuntimeException('Only destination ports 80 and 443 are allowed.');
        }
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $addresses = [$host];
        } else {
            // Reject legacy numeric IP forms before DNS resolution (decimal, octal and hexadecimal).
            if (! filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)
                || ! str_contains($host, '.') || preg_match('/^[0-9.]+$/', $host)
                || preg_match('/(^|\.)0x[0-9a-f]+(\.|$)/i', $host)) {
                throw new RuntimeException('Invalid public hostname.');
            }
            $addresses = $this->resolveAddresses($host);
        }
        if ($addresses === []) {
            throw new RuntimeException('Destination could not be resolved.');
        }
        foreach ($addresses as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_GLOBAL_RANGE)
                || IpUtils::isPrivateIp($address)
                || IpUtils::checkIp($address, ['224.0.0.0/4', '192.0.0.0/24', '100::/64', '2001::/23', 'fec0::/10', 'ff00::/8'])) {
                throw new RuntimeException('Private and reserved network destinations are blocked.');
            }
        }

        return [$host, $port, $addresses[0]];
    }

    protected function resolveAddresses(string $host): array
    {
        // DNS names use IPv4 only. cURL is pinned to the checked answer, never to a second lookup.
        return gethostbynamel($host) ?: [];
    }

    private function request(string $method, string $url, array $payload = []): Response
    {
        [$host, $port, $address] = $this->destination($url);
        if (! extension_loaded('curl')) {
            throw new RuntimeException('The cURL extension is required for outbound requests.');
        }
        $isIpv6 = str_contains($address, ':');
        $response = Http::connectTimeout(5)->timeout(15)->withHeaders([
            'User-Agent' => 'BloomFeed/1.0',
            'Accept-Encoding' => 'identity',
        ])->withOptions([
            'allow_redirects' => false,
            'decode_content' => false,
            'proxy' => '',
            'verify' => true,
            'force_ip_resolve' => $isIpv6 ? 'v6' : 'v4',
            'protocols' => ['http', 'https'],
            'curl' => [
                CURLOPT_RESOLVE => filter_var($host, FILTER_VALIDATE_IP) ? [] : [$host.':'.$port.':'.$address],
            ],
            'on_headers' => static function (ResponseInterface $response): void {
                if ((int) $response->getHeaderLine('Content-Length') > self::MAX_BYTES
                    || ! in_array(strtolower($response->getHeaderLine('Content-Encoding')), ['', 'identity'], true)) {
                    throw new RuntimeException('Response is too large or uses unsupported compression.');
                }
            },
            'progress' => static function ($total, $downloaded): void {
                if ($downloaded > self::MAX_BYTES) {
                    throw new RuntimeException('Response exceeds the download limit.');
                }
            },
        ])->send($method, $url, $method === 'POST' ? ['json' => $payload] : []);
        // Also applies to mocked responses, which bypass cURL transfer callbacks.
        if (strlen($response->body()) > self::MAX_BYTES) {
            throw new RuntimeException('Response exceeds the download limit.');
        }

        return $response;
    }
}

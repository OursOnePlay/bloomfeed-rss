<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustedNetwork
{
    public function handle(Request $request, Closure $next): Response
    {
        Request::setTrustedProxies(config('security.trusted_proxies', []), Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PORT);
        $hosts = config('security.trusted_hosts', []);
        if ($hosts !== [] && ! in_array(strtolower($request->getHost()), array_map('strtolower', $hosts), true)) {
            abort(400, 'Unrecognized host.');
        }

        return $next($request);
    }
}

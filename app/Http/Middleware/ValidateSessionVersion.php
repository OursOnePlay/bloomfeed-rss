<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateSessionVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            if (Auth::guard('web')->viaRemember()) {
                $request->session()->put('auth_version', (int) $user->auth_version);
            }
            if ((int) $request->session()->get('auth_version', 0) !== (int) $user->auth_version) {
                Auth::guard('web')->logoutCurrentDevice();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                throw new AuthenticationException;
            }
        }

        return $next($request);
    }
}

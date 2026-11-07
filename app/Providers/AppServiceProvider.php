<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = $request->input('email');
            $account = is_string($email) ? mb_strtolower(trim($email)) : '';

            return [
                Limit::perMinute(5)->by('login-account:'.hash('sha256', $account.'|'.$request->ip())),
                Limit::perMinute(30)->by('login-ip:'.$request->ip()),
            ];
        });
        RateLimiter::for('registration', fn (Request $request) => Limit::perMinute(3)->by('register:'.$request->ip()));
        RateLimiter::for('network', fn (Request $request) => Limit::perMinute(6)->by('network:'.$request->user()->id));
        RateLimiter::for('bulk-refresh', fn (Request $request) => Limit::perMinutes(5, 1)->by('refresh:'.$request->user()->id));

        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}

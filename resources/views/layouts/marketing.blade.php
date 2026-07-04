<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BloomFeed' }}</title>
    <meta name="description" content="{{ __('app.meta_description') }}">
    <link rel="icon" href="{{ asset('images/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        (function () {
            var stored = localStorage.getItem('bloomfeed-theme');
            var theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>
    <nav class="navbar navbar-marketing">
        <a href="{{ route('home') }}" class="navbar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="BloomFeed" class="brand-logo brand-logo-light">
            <img src="{{ asset('images/logo-dark.png') }}" alt="BloomFeed" class="brand-logo brand-logo-dark">
        </a>
        <div class="navbar-links">
            <a href="{{ route('self-hosting') }}" class="{{ request()->routeIs('self-hosting') ? 'active' : '' }}">{{ __('app.self_hosting') }}</a>
            @include('partials.locale-selector')
            <button type="button" class="theme-toggle" data-theme-toggle title="{{ __('app.change_theme') }}">◐</button>
            @if (\App\Models\User::query()->exists() && ! \App\Models\Setting::registrationsOpen())
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">{{ __('app.login') }}</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">{{ __('app.create_account') }}</a>
            @endif
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <span>{{ __('app.footer_tagline') }}</span>
        <a href="https://git.eldorianet.work/esteban/BloomFeed-RSS" target="_blank" rel="noopener">{{ __('app.source_code') }}</a>
    </footer>

    <script src="{{ asset('js/theme-toggle.js') }}" defer></script>
</body>
</html>

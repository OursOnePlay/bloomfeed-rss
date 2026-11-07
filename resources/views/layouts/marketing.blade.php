<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BloomFeed' }}</title>
    <meta name="description" content="{{ $description ?? __('app.meta_description') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('images/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="BloomFeed">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">
    <meta property="og:title" content="{{ $title ?? 'BloomFeed' }}">
    <meta property="og:description" content="{{ $description ?? __('app.meta_description') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:width" content="1917">
    <meta property="og:image:height" content="544">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'BloomFeed' }}">
    <meta name="twitter:description" content="{{ $description ?? __('app.meta_description') }}">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    @stack('structured-data')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        (function () {
            var stored;
            try { stored = localStorage.getItem('bloomfeed-theme'); } catch (_) {}
            var theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body class="marketing">
    <a class="skip-link" href="#main-content">{{ __('design.skip') }}</a>
    <nav class="navbar navbar-marketing">
        <a href="{{ route('home') }}" class="navbar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="BloomFeed" class="brand-logo brand-logo-light">
            <img src="{{ asset('images/logo-dark.png') }}" alt="BloomFeed" class="brand-logo brand-logo-dark">
        </a>
        <button type="button" class="navbar-toggle" data-navbar-toggle aria-expanded="false" aria-controls="navbarLinks" title="{{ __('app.nav_menu') }}">
            <span class="navbar-toggle-bar"></span>
            <span class="navbar-toggle-bar"></span>
            <span class="navbar-toggle-bar"></span>
        </button>
        <div class="navbar-links" id="navbarLinks">
            <a href="{{ route('self-hosting') }}" class="nav-self-hosting {{ request()->routeIs('self-hosting') ? 'active' : '' }}">{{ __('app.self_hosting') }}</a>
            @include('partials.locale-selector')
            <button type="button" class="theme-toggle" data-theme-toggle title="{{ __('app.change_theme') }}">◐</button>
            @if (\App\Models\User::query()->exists() && ! \App\Models\Setting::registrationsOpen())
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">{{ __('app.login') }}</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">{{ __('app.create_account') }}</a>
            @endif
        </div>
    </nav>

    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    <footer class="site-footer">
        <span>{{ __('app.footer_tagline') }}</span>
        <a href="https://git.eldorianet.work/esteban/BloomFeed-RSS" target="_blank" rel="noopener">{{ __('app.source_code') }}</a>
    </footer>

    <div class="live-message" role="status" aria-live="polite" data-live-message></div>
    <script src="{{ asset('js/theme-toggle.js') }}" defer></script>
    <script src="{{ asset('js/navbar-toggle.js') }}" defer></script>
    <script src="{{ asset('js/scroll-reveal.js') }}" defer></script>
</body>
</html>

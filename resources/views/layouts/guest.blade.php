<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BloomFeed' }}</title>
    <link rel="icon" href="{{ asset('images/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
<body class="guest-page">
    <a class="skip-link" href="#main-content">{{ __('design.skip') }}</a>
    <div class="guest-topbar">
        @include('partials.locale-selector')
        <button type="button" class="theme-toggle" data-theme-toggle title="{{ __('app.change_theme') }}">◐</button>
    </div>
    <main class="centered-shell" id="main-content" tabindex="-1">
        @yield('content')
    </main>
    <footer class="site-footer">@include('partials.credits')</footer>

    <div class="live-message" role="status" aria-live="polite" data-live-message></div>
    <script src="{{ asset('js/theme-toggle.js') }}" defer></script>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>

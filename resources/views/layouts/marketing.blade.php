<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BloomFeed' }}</title>
    <meta name="description" content="BloomFeed est un agrégateur RSS personnel et auto-hébergé, avec lecture approfondie intégrée.">
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
            <a href="{{ route('self-hosting') }}" class="{{ request()->routeIs('self-hosting') ? 'active' : '' }}">Self-hosting</a>
            <button type="button" class="theme-toggle" data-theme-toggle title="Changer de thème">◐</button>
            @if (\App\Models\User::query()->exists())
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Se connecter</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Créer un compte</a>
            @endif
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <span>BloomFeed — projet open-source, auto-hébergé.</span>
        <a href="https://git.eldorianet.work/esteban/BloomFeed-RSS" target="_blank" rel="noopener">Code source</a>
    </footer>

    <script src="{{ asset('js/theme-toggle.js') }}" defer></script>
</body>
</html>

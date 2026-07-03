<nav class="navbar">
    <a href="{{ route('dashboard') }}" class="navbar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="BloomFeed" class="brand-logo brand-logo-light">
        <img src="{{ asset('images/logo-dark.png') }}" alt="BloomFeed" class="brand-logo brand-logo-dark">
    </a>
    <div class="navbar-links">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Articles</a>
        <a href="{{ route('catalog') }}" class="{{ request()->routeIs('catalog') ? 'active' : '' }}">Catalogue</a>
        <a href="{{ route('feeds.index') }}" class="{{ request()->routeIs('feeds.*') ? 'active' : '' }}">Mes flux</a>
        <button type="button" class="theme-toggle" data-theme-toggle title="Changer de thème">◐</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm">Déconnexion</button>
        </form>
    </div>
</nav>

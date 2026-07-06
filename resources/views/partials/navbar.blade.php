<nav class="navbar">
    <a href="{{ route('dashboard') }}" class="navbar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="BloomFeed" class="brand-logo brand-logo-light">
        <img src="{{ asset('images/logo-dark.png') }}" alt="BloomFeed" class="brand-logo brand-logo-dark">
    </a>
    <button type="button" class="navbar-toggle" data-navbar-toggle aria-expanded="false" aria-controls="navbarLinks" title="{{ __('app.nav_menu') }}">
        <span class="navbar-toggle-bar"></span>
        <span class="navbar-toggle-bar"></span>
        <span class="navbar-toggle-bar"></span>
    </button>
    <div class="navbar-links" id="navbarLinks">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">{{ __('app.articles') }}</a>
        <a href="{{ route('catalog') }}" class="{{ request()->routeIs('catalog') ? 'active' : '' }}">{{ __('app.catalog') }}</a>
        <a href="{{ route('feeds.index') }}" class="{{ request()->routeIs('feeds.*') ? 'active' : '' }}">{{ __('app.my_feeds') }}</a>
        <a href="{{ route('notifiers') }}" class="{{ request()->routeIs('notifiers') ? 'active' : '' }}">{{ __('app.notifiers') }}</a>
        @if (auth()->user()?->is_owner)
            <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}">{{ __('app.settings') }}</a>
        @endif
        @include('partials.locale-selector')
        <button type="button" class="theme-toggle" data-theme-toggle title="{{ __('app.change_theme') }}">◐</button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm">{{ __('app.logout') }}</button>
        </form>
    </div>
</nav>

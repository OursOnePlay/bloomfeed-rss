@extends('layouts.marketing', ['title' => 'BloomFeed'])

@section('content')
<section class="hero">
    <div class="hero-orbs" aria-hidden="true">
        <span class="orb orb-1"></span>
        <span class="orb orb-2"></span>
        <span class="orb orb-3"></span>
    </div>

    <div class="hero-float-icon hero-float-icon-1" aria-hidden="true"><i class="fa-solid fa-rss"></i></div>
    <div class="hero-float-icon hero-float-icon-2" aria-hidden="true"><i class="fa-solid fa-bell"></i></div>
    <div class="hero-float-icon hero-float-icon-3" aria-hidden="true"><i class="fa-solid fa-book-open"></i></div>
    <div class="hero-float-icon hero-float-icon-4" aria-hidden="true"><i class="fa-solid fa-house-laptop"></i></div>

    <div class="hero-grid">
        <div class="hero-inner">
            <span class="eyebrow">{{ __('app.hero_eyebrow') }}</span>
            <h1>{{ __('app.hero_title') }}</h1>
            <p class="hero-lede">{{ __('app.hero_lede') }}</p>
            <div class="hero-cta">
                @if ($hasOwner && ! \App\Models\Setting::registrationsOpen())
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">{{ __('app.login') }}</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('app.create_account') }}</a>
                @endif
                <a href="{{ route('self-hosting') }}" class="btn btn-ghost btn-lg">{{ __('app.how_to_self_host') }}</a>
            </div>

            <div class="trust-badges">
                <span class="trust-badge reveal"><i class="fa-solid fa-server"></i> {{ __('app.badge_self_hosted') }}</span>
                <span class="trust-badge reveal"><i class="fa-brands fa-git-alt"></i> {{ __('app.badge_open_source') }}</span>
                <span class="trust-badge reveal"><i class="fa-solid fa-scale-balanced"></i> {{ __('app.badge_mit_license') }}</span>
                <span class="trust-badge reveal"><i class="fa-solid fa-language"></i> {{ __('app.badge_four_languages') }}</span>
            </div>
        </div>

        <div class="hero-visual">
            <div class="browser-frame">
                <div class="browser-frame-bar">
                    <span class="browser-dot browser-dot-red"></span>
                    <span class="browser-dot browser-dot-yellow"></span>
                    <span class="browser-dot browser-dot-green"></span>
                </div>
                <img src="{{ asset('images/screenshots/dashboard-light.png') }}" alt="BloomFeed dashboard" class="browser-frame-img browser-frame-img-light">
                <img src="{{ asset('images/screenshots/dashboard-dark.png') }}" alt="BloomFeed dashboard" class="browser-frame-img browser-frame-img-dark">
            </div>
        </div>
    </div>
</section>

<section class="feature-grid">
    <div class="feature-card reveal">
        <div class="feature-icon feature-icon-1"><i class="fa-solid fa-rss"></i></div>
        <h3>{{ __('app.feature_feeds_title') }}</h3>
        <p>{{ __('app.feature_feeds_text') }}</p>
    </div>
    <div class="feature-card reveal">
        <div class="feature-icon feature-icon-2"><i class="fa-solid fa-book-open"></i></div>
        <h3>{{ __('app.feature_reader_title') }}</h3>
        <p>{{ __('app.feature_reader_text') }}</p>
    </div>
    <div class="feature-card reveal">
        <div class="feature-icon feature-icon-3"><i class="fa-solid fa-house-lock"></i></div>
        <h3>{{ __('app.feature_home_title') }}</h3>
        <p>{{ __('app.feature_home_text') }}</p>
    </div>
    <div class="feature-card reveal">
        <div class="feature-icon feature-icon-4"><i class="fa-solid fa-bell"></i></div>
        <h3>{{ __('app.notifiers') }}</h3>
        <p>{{ __('app.notifiers_lede') }}</p>
    </div>
</section>

<section class="screenshots-section">
    <div class="screenshots-header reveal">
        <h2>{{ __('app.screenshots_title') }}</h2>
        <p>{{ __('app.screenshots_lede') }}</p>
    </div>

    @php
        $screenshots = [
            ['file' => 'dashboard-light.png', 'caption' => __('app.screenshot_dashboard')],
            ['file' => 'dashboard-dark.png', 'caption' => __('app.screenshot_dark')],
            ['file' => 'reader-light.png', 'caption' => __('app.screenshot_reader')],
            ['file' => 'catalog-light.png', 'caption' => __('app.screenshot_catalog')],
            ['file' => 'notifiers-light.png', 'caption' => __('app.screenshot_notifiers')],
        ];
    @endphp

    <div class="screenshot-gallery reveal">
        <div class="browser-frame screenshot-main">
            <div class="browser-frame-bar">
                <span class="browser-dot browser-dot-red"></span>
                <span class="browser-dot browser-dot-yellow"></span>
                <span class="browser-dot browser-dot-green"></span>
            </div>
            <img id="screenshotMainImg" class="browser-frame-img" src="{{ asset('images/screenshots/' . $screenshots[0]['file']) }}" alt="{{ $screenshots[0]['caption'] }}">
        </div>
        <div class="screenshot-thumbs">
            @foreach ($screenshots as $index => $shot)
                <button type="button" class="screenshot-thumb {{ $index === 0 ? 'active' : '' }}" data-img="{{ asset('images/screenshots/' . $shot['file']) }}" data-caption="{{ $shot['caption'] }}">
                    <img src="{{ asset('images/screenshots/' . $shot['file']) }}" alt="{{ $shot['caption'] }}" loading="lazy">
                </button>
            @endforeach
        </div>
        <p class="screenshot-caption" id="screenshotCaption">{{ $screenshots[0]['caption'] }}</p>
    </div>
</section>

<script src="{{ asset('js/screenshot-gallery.js') }}" defer></script>
@endsection

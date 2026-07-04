@extends('layouts.marketing', ['title' => 'BloomFeed'])

@section('content')
<section class="hero">
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
    </div>
</section>

<section class="feature-grid">
    <div class="feature-card">
        <div class="feature-icon">📰</div>
        <h3>{{ __('app.feature_feeds_title') }}</h3>
        <p>{{ __('app.feature_feeds_text') }}</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">📖</div>
        <h3>{{ __('app.feature_reader_title') }}</h3>
        <p>{{ __('app.feature_reader_text') }}</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🔒</div>
        <h3>{{ __('app.feature_home_title') }}</h3>
        <p>{{ __('app.feature_home_text') }}</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🔔</div>
        <h3>{{ __('app.notifiers') }}</h3>
        <p>{{ __('app.notifiers_lede') }}</p>
    </div>
</section>

<section class="screenshots-section">
    <div class="screenshots-header">
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

    <div class="screenshot-gallery">
        <div class="screenshot-main">
            <img id="screenshotMainImg" src="{{ asset('images/screenshots/' . $screenshots[0]['file']) }}" alt="{{ $screenshots[0]['caption'] }}">
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

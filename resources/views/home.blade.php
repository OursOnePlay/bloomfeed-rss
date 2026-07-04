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
</section>
@endsection

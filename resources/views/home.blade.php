@extends('layouts.marketing', ['title' => __('app.seo_home_title'), 'description' => __('app.meta_description')])
@section('content')
@php
    $screenshotUrl = fn ($file) => asset('images/preview/'.$file);
    $entryRoute = $hasOwner && ! \App\Models\Setting::registrationsOpen() ? route('login') : route('register');
    $entryLabel = $hasOwner && ! \App\Models\Setting::registrationsOpen() ? __('app.login') : __('app.create_account');
@endphp
<section class="marketing-hero">
    <h1>{{ __('design.hero_title') }}</h1>
    <div class="hero-intro"><p>{{ __('design.hero_lede') }}</p><div class="hero-actions"><a href="{{ $entryRoute }}" class="btn btn-primary btn-lg">{{ $entryLabel }}</a><a href="{{ route('self-hosting') }}">{{ __('app.self_hosting') }}</a></div></div>
    <div class="product-stage" data-ui><img src="{{ $screenshotUrl('dashboard-light.png') }}" alt="{{ __('app.screenshot_dashboard') }}" width="1440" height="900" class="browser-frame-img-light"><img src="{{ $screenshotUrl('dashboard-dark.png') }}" alt="{{ __('app.screenshot_dark') }}" width="1440" height="900" class="browser-frame-img-dark"></div>
</section>
<section class="marketing-showcase">
    <div class="section-intro"><h2 class="reveal">{{ __('design.showcase_title') }}</h2><p class="reveal">{{ __('design.showcase_lede') }}</p></div>
    <div class="preview-switch" data-preview-switch aria-label="{{ __('app.change_theme') }}"><button type="button" data-preview="light" aria-pressed="true">{{ __('design.light') }}</button><button type="button" data-preview="dark" aria-pressed="false">{{ __('design.dark') }}</button></div>
    <div class="showcase-board">
        @foreach (['reader' => 'screenshot_reader', 'catalog' => 'screenshot_catalog', 'notifiers' => 'screenshot_notifiers'] as $screen => $caption)
            <figure><img class="reveal" src="{{ $screenshotUrl($screen.'-light.png') }}" data-preview-image="{{ asset('images/preview/'.$screen) }}" alt="{{ __('app.'.$caption) }}" width="1440" height="900" loading="lazy"><figcaption class="reveal">{{ __('app.'.$caption) }}</figcaption></figure>
        @endforeach
    </div>
</section>
<section class="marketing-features">
    <div class="feature-statement"><h2 class="reveal">{{ __('design.home_title') }}</h2><p class="reveal">{{ __('design.home_lede') }}</p></div>
    <div class="feature-rows">
        @foreach (['feeds', 'reader', 'multiuser'] as $feature)
            <div class="feature-row"><h3 class="reveal">{{ __('app.feature_'.$feature.'_title') }}</h3><p class="reveal">{{ __('app.feature_'.$feature.'_text') }}</p></div>
        @endforeach
    </div>
</section>
<a href="{{ route('self-hosting') }}" class="marketing-cta"><p class="reveal">{{ __('design.install_lede') }}</p><span class="reveal">{{ __('design.install') }}</span></a>
<script src="{{ asset('js/marketing.js') }}" defer></script>
@push('structured-data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "BloomFeed",
    "applicationCategory": "CommunicationApplication",
    "operatingSystem": "Linux (Docker)",
    "description": {!! json_encode(__('app.meta_description')) !!},
    "url": {!! json_encode(url('/')) !!},
    "image": {!! json_encode(asset('images/logo.png')) !!},
    "license": "https://opensource.org/licenses/MIT",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
    },
    "author": {
        "@type": "Organization",
        "name": "BloomFeed",
        "url": {!! json_encode(url('/self-hosting')) !!}
    }
}
</script>
@endpush
@endsection

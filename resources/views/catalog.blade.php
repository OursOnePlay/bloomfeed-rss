@extends('layouts.app', ['title' => __('app.catalog').' — BloomFeed'])

@section('content')
<div class="main-column" style="width:100%;">
    @if (session('status'))
        <div class="form-status">{{ session('status') }}</div>
    @endif

    <div class="market-header">
        <div>
            <h2 style="margin:0;">{{ __('app.catalog_title') }}</h2>
            <p class="text-muted text-sm" style="margin:4px 0 0;">
                {{ __('app.catalog_lede') }}
            </p>
        </div>
    </div>

    @if ($feeds->isEmpty())
        <div class="card empty-state">
            {{ __('app.catalog_empty') }}
        </div>
    @else
        @php($languageNames = [
            'fr' => 'Français', 'en' => 'English', 'es' => 'Español', 'de' => 'Deutsch',
            'it' => 'Italiano', 'pt' => 'Português', 'nl' => 'Nederlands', 'ja' => '日本語',
            'ru' => 'Русский', 'pl' => 'Polski', 'sv' => 'Svenska', 'zh' => '中文',
            'ko' => '한국어', 'ar' => 'العربية', 'tr' => 'Türkçe', 'hi' => 'हिन्दी',
            'no' => 'Norsk', 'da' => 'Dansk', 'fi' => 'Suomi', 'cs' => 'Čeština',
            'el' => 'Ελληνικά', 'he' => 'עברית', 'uk' => 'Українська', 'ro' => 'Română',
            'hu' => 'Magyar', 'id' => 'Bahasa Indonesia', 'vi' => 'Tiếng Việt', 'th' => 'ไทย',
            'sk' => 'Slovenčina', 'bg' => 'Български', 'hr' => 'Hrvatski', 'lt' => 'Lietuvių',
            'lv' => 'Latviešu', 'et' => 'Eesti', 'sr' => 'Српски', 'ca' => 'Català',
            'fa' => 'فارسی', 'sw' => 'Kiswahili', 'ur' => 'اردو', 'bn' => 'বাংলা',
            'ne' => 'नेपाली', 'ps' => 'پښتو', 'so' => 'Soomaali', 'am' => 'አማርኛ',
            'ig' => 'Igbo', 'ha' => 'Hausa', 'yo' => 'Yorùbá', 'az' => 'Azərbaycan',
            'uz' => 'Oʻzbek', 'ky' => 'Кыргызча', 'mr' => 'मराठी', 'ta' => 'தமிழ்',
            'te' => 'తెలుగు', 'gu' => 'ગુજરાતી', 'pa' => 'ਪੰਜਾਬੀ', 'si' => 'සිංහල',
        ])
        @php($languages = $feeds->flatten(1)->pluck('lang')->unique()->sort()->values())

        <div class="market-toolbar">
            <input type="search" id="market-search" class="form-input market-search" placeholder="{{ __('app.search_placeholder') }}">
            <select id="market-lang" class="form-input market-lang-select">
                <option value="">{{ __('app.all_languages') }}</option>
                @foreach ($languages as $lang)
                    <option value="{{ $lang }}">{{ $languageNames[$lang] ?? strtoupper($lang) }}</option>
                @endforeach
            </select>
        </div>

        <div class="market-chips" id="market-chips">
            <button type="button" class="chip active" data-category="">{{ __('app.all') }}</button>
            @foreach ($feeds->keys() as $category)
                <button type="button" class="chip" data-category="{{ $category }}">{{ $category }}</button>
            @endforeach
        </div>

        <div class="market-grid" id="market-grid">
            @foreach ($feeds as $category => $items)
                @foreach ($items as $item)
                    <div class="market-card" data-category="{{ $category }}" data-lang="{{ $item['lang'] }}" data-search="{{ Str::lower($item['title'].' '.$item['description'].' '.$category) }}">
                        <div class="market-card-head">
                            <span class="market-card-title">{{ $item['title'] }}</span>
                            <span class="chip chip-static">{{ $category }}</span>
                        </div>
                        <p class="market-card-desc">{{ $item['description'] ?: $item['url'] }}</p>
                        <div class="market-card-foot">
                            <span class="market-lang">{{ strtoupper($item['lang']) }}</span>
                            @if (in_array($item['url'], $subscribedUrls))
                                <span class="market-added">✓ {{ __('app.added') }}</span>
                            @else
                                <form method="POST" action="{{ route('feeds.store') }}">
                                    @csrf
                                    <input type="hidden" name="url" value="{{ $item['url'] }}">
                                    <input type="hidden" name="title" value="{{ $item['title'] }}">
                                    <button type="submit" class="btn btn-primary btn-sm">+ {{ __('app.add') }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

        <div class="card empty-state" id="market-empty" hidden>{{ __('app.no_match') }}</div>
    @endif
</div>
@endsection

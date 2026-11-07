@extends('layouts.app', ['title' => __('app.catalog').' - BloomFeed'])

@section('content')
<div class="main-column" style="width:100%;">
    @if (session('status'))
        <div class="form-status" role="status">{{ session('status') }}</div>
    @endif

    <div class="market-header">
        <div>
            <h1 style="margin:0;">{{ __('app.catalog_title') }}</h1>
            <p class="text-muted text-sm" style="margin:4px 0 0;">
                {{ __('app.catalog_lede') }}
            </p>
        </div>
    </div>

    @if (!$hasCatalog)
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


        <form class="market-toolbar" id="catalog-filters" method="GET" action="{{ route('catalog') }}">
            <label class="sr-only" for="market-search">{{ __('app.search_placeholder') }}</label>
            <input type="search" id="market-search" name="q" value="{{ request('q') }}" class="form-input market-search" placeholder="{{ __('app.search_placeholder') }}">
            <div class="language-filter"><label for="market-lang">{{ __('app.language') }}</label><select id="market-lang" name="lang" class="market-lang-select">
                <option value="">{{ __('app.all_languages') }}</option>
                @foreach ($languages as $lang)
                    <option value="{{ $lang }}" @selected(request('lang') === $lang)>{{ $languageNames[$lang] ?? strtoupper($lang) }}</option>
                @endforeach
            </select></div>
            <button class="btn btn-ghost" type="submit">{{ __('design.search') }}</button>
            <input type="hidden" name="category" value="{{ request('category') }}">
        </form>

        <div class="market-chips" id="market-chips">
            <button form="catalog-filters" type="submit" name="category" value="" class="chip {{ !request('category') ? 'active' : '' }}" aria-pressed="{{ !request('category') ? 'true' : 'false' }}" data-category="">{{ __('app.all') }}</button>
            @foreach ($categories as $category)
                <button form="catalog-filters" type="submit" name="category" value="{{ $category }}" class="chip {{ request('category') === $category ? 'active' : '' }}" aria-pressed="{{ request('category') === $category ? 'true' : 'false' }}" data-category="{{ $category }}">{{ $category }}</button>
            @endforeach
        </div>

        <div class="market-grid" id="market-grid">
            @include('partials.catalog-cards')
        </div>

        <div class="card empty-state" id="market-empty" @if ($feeds->total() > 0) hidden @endif>{{ __('app.no_match') }}</div>
        <p class="form-error" role="alert" id="catalog-error" hidden>{{ __('design.action_error') }}</p>
        <a class="btn btn-ghost" id="catalog-more" href="{{ $feeds->nextPageUrl() ?? '' }}" @if (!$feeds->hasMorePages()) hidden @endif>{{ __('catalog.more') }}</a>
        <div id="catalog-fallback">{{ $feeds->onEachSide(1)->links('partials.pagination') }}</div>
    @endif
</div>
@endsection

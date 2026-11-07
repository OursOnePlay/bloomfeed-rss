@extends('layouts.app', ['title' => __('app.articles').' - BloomFeed'])
@section('content')
<aside class="sidebar" aria-label="{{ __('app.feeds_label') }}">@include('partials.feed-sidebar')</aside>
<div class="main-column" data-reading-list>
    @if (session('status'))<div class="form-status" role="status">{{ session('status') }}</div>@endif
    <header class="page-heading">
        <div><h1>{{ $selectedFeed?->displayTitle() ?? __('app.latest_articles') }}</h1>
        <p>{{ __('app.articles_count', ['count' => $articles->total()]) }}</p></div>
        <a href="{{ route('feeds.index') }}" class="btn btn-primary">{{ __('app.add_feed') }}</a>
    </header>
    <div class="page-toolbar">
        <form class="search-form" action="{{ route('dashboard') }}" method="GET" role="search">
            @foreach (['feed', 'unread', 'favorite'] as $filter)
                @if (request($filter))<input type="hidden" name="{{ $filter }}" value="{{ request($filter) }}">@endif
            @endforeach
            <label for="article-search" class="sr-only">{{ __('design.search_articles') }}</label>
            <input id="article-search" name="q" type="search" class="form-input" value="{{ request('q') }}" placeholder="{{ __('design.search_articles') }}" maxlength="200">
            <button class="btn btn-ghost" type="submit">{{ __('design.search') }}</button>
        </form>
        <label class="density-control"><input type="checkbox" data-density-toggle> {{ __('design.compact') }}</label>
    </div>
    <nav class="view-tabs" aria-label="{{ __('design.article_filters') }}">
        @foreach (['all' => __('app.all'), 'unread' => __('design.unread'), 'favorite' => __('design.favorites')] as $filter => $label)
            <a href="{{ route('dashboard', array_filter(['feed' => request('feed'), 'q' => request('q'), 'unread' => $filter === 'unread' ? 1 : null, 'favorite' => $filter === 'favorite' ? 1 : null])) }}" @if (($filter === 'all' && !request('unread') && !request('favorite')) || request($filter)) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
    @if ($articles->isNotEmpty())
        <div class="article-list">@foreach ($articles as $article) @include('partials.article-card') @endforeach</div>
    @else
        <div class="card empty-state">
            <p>{{ $feeds->isEmpty() ? __('design.start_reading') : __('design.no_results') }}</p>
            <a class="btn btn-ghost" href="{{ $feeds->isEmpty() ? route('catalog') : route('dashboard') }}">{{ $feeds->isEmpty() ? __('app.catalog') : __('app.show_all') }}</a>
        </div>
    @endif
    {{ $articles->onEachSide(1)->links('partials.pagination') }}
</div>
@endsection

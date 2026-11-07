@extends('layouts.app', ['title' => $article->title.' - BloomFeed'])

@section('content')
<div class="reader-shell">
    @if (session('status'))
        <div class="form-status" role="status">{{ session('status') }}</div>
    @endif

    <a href="{{ url()->previous(route('dashboard')) }}" class="back-link">&larr; {{ __('app.back_to_articles') }}</a>

    <article class="card reader-article">
        <h1 class="reader-title">{{ $article->title }}</h1>
        <div class="reader-meta">
            <span>{{ $article->feed->displayTitle() }}</span>
            @if ($article->published_at)
                <span>&middot; {{ $article->published_at->diffForHumans() }}</span>
            @endif
            @if (in_array(strtolower((string) parse_url($article->link, PHP_URL_SCHEME)), ['http', 'https'], true))
            <a href="{{ $article->link }}" target="_blank" rel="noopener" class="reader-source-link">{{ __('app.view_original') }} &#8599;</a>
            @endif
        </div>

        @if ($article->full_content_error)
            <div class="reader-error">
                <p>{{ __('app.fetch_error', ['error' => $article->full_content_error]) }}</p>
                <form method="POST" action="{{ route('articles.retry', $article) }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">{{ __('app.retry') }}</button>
                </form>
                @if ($article->excerpt)
                    <p class="mt-2">{{ $article->excerpt }}</p>
                @endif
            </div>
        @elseif ($contentHtml)
            <div class="prose">{!! $contentHtml !!}</div>
        @else
            <p class="text-muted">{{ __('app.fetching') }}</p>
        @endif
    </article>
</div>
@endsection

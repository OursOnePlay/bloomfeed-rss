@extends('layouts.app', ['title' => $article->title.' — BloomFeed'])

@section('content')
<div class="reader-shell">
    @if (session('status'))
        <div class="form-status">{{ session('status') }}</div>
    @endif

    <a href="{{ url()->previous(route('dashboard')) }}" class="back-link">&larr; Retour aux articles</a>

    <article class="card reader-article">
        <h1 class="reader-title">{{ $article->title }}</h1>
        <div class="reader-meta">
            <span>{{ $article->feed->displayTitle() }}</span>
            @if ($article->published_at)
                <span>&middot; {{ $article->published_at->diffForHumans() }}</span>
            @endif
            <a href="{{ $article->link }}" target="_blank" rel="noopener" class="reader-source-link">Voir la source originale &#8599;</a>
        </div>

        @if ($article->full_content_error)
            <div class="reader-error">
                <p>Impossible de récupérer le contenu complet de cet article ({{ $article->full_content_error }}).</p>
                <form method="POST" action="{{ route('articles.retry', $article) }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Réessayer</button>
                </form>
                @if ($article->excerpt)
                    <p class="mt-2">{{ $article->excerpt }}</p>
                @endif
            </div>
        @elseif ($contentHtml)
            <div class="prose">{!! $contentHtml !!}</div>
        @else
            <p class="text-muted">Récupération du contenu en cours…</p>
        @endif
    </article>
</div>
@endsection

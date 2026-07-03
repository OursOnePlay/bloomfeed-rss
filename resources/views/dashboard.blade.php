@extends('layouts.app', ['title' => 'Articles — BloomFeed'])

@section('content')
<div class="sidebar">
    @include('partials.feed-sidebar')
</div>

<div class="main-column">
    @if (session('status'))
        <div class="form-status">{{ session('status') }}</div>
    @endif

    <div class="flex-between">
        <h2 style="margin:0;">Derniers articles</h2>
        <div class="flex" style="gap:8px;">
            <a href="{{ route('dashboard', array_filter(['feed' => request('feed'), 'unread' => request('unread') ? null : 1])) }}"
               class="btn btn-ghost btn-sm">
                {{ request('unread') ? 'Tout afficher' : 'Non lus uniquement' }}
            </a>
        </div>
    </div>

    @forelse ($articles as $article)
        @include('partials.article-card', ['article' => $article])
    @empty
        <div class="card empty-state">
            @if ($feeds->isEmpty())
                Ajoutez votre premier flux RSS depuis la barre latérale pour commencer.
            @else
                Aucun article pour le moment. Essayez d'actualiser vos flux.
            @endif
        </div>
    @endforelse

    <div class="pagination">
        {{ $articles->links() }}
    </div>
</div>
@endsection

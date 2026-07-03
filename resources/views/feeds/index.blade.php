@extends('layouts.app', ['title' => 'Mes flux — BloomFeed'])

@section('content')
<div class="main-column" style="width:100%;">
    @if (session('status'))
        <div class="form-status">{{ session('status') }}</div>
    @endif

    <div class="card">
        <h2>Ajouter un flux</h2>
        <form method="POST" action="{{ route('feeds.store') }}" class="inline-form">
            @csrf
            <input type="url" name="url" class="form-input" placeholder="https://exemple.com/rss.xml" required>
            <input type="text" name="title" class="form-input" placeholder="Titre personnalisé (optionnel)" style="max-width:220px;">
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
        @error('url')
            <div class="form-error mt-1">{{ $message }}</div>
        @enderror
    </div>

    <h2 class="mt-3">Mes flux</h2>

    @forelse ($feeds as $feed)
        <div class="card flex-between">
            <div>
                <div style="font-weight:600;">{{ $feed->displayTitle() }}</div>
                <div class="text-sm text-muted">{{ $feed->url }}</div>
                <div class="text-sm text-muted mt-1">
                    {{ $feed->articles_count }} article(s)
                    @if ($feed->last_fetched_at)
                        &middot; actualisé {{ $feed->last_fetched_at->diffForHumans() }}
                    @endif
                    @if ($feed->last_fetch_status && $feed->last_fetch_status !== 'ok')
                        &middot; <span style="color:var(--danger);">{{ $feed->last_fetch_status }}</span>
                    @endif
                </div>
            </div>
            <div class="flex" style="gap:8px;">
                <form method="POST" action="{{ route('feeds.refresh', $feed) }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Actualiser</button>
                </form>
                <form method="POST" action="{{ route('feeds.destroy', $feed) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Supprimer ce flux et tous ses articles ?">Supprimer</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card empty-state">Vous n'avez pas encore ajouté de flux.</div>
    @endforelse
</div>
@endsection

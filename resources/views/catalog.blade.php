@extends('layouts.app', ['title' => 'Catalogue — BloomFeed'])

@section('content')
<div class="main-column" style="width:100%;">
    @if (session('status'))
        <div class="form-status">{{ session('status') }}</div>
    @endif

    <div class="market-header">
        <div>
            <h2 style="margin:0;">Catalogue de flux</h2>
            <p class="text-muted text-sm" style="margin:4px 0 0;">
                Une sélection de flux RSS maintenue par BloomFluxDB — ajoutez-les à votre collection en un clic.
            </p>
        </div>
    </div>

    @if ($fromCache)
        <div class="cache-notice">
            ⚠ Serveur catalogue injoignable — liste servie depuis le cache local
            @if ($updatedAt)
                (mise à jour {{ $updatedAt->diffForHumans() }})
            @endif
        </div>
    @endif

    @if ($feeds->isEmpty())
        <div class="card empty-state">
            Le catalogue est vide pour le moment.
            @if ($fromCache)
                Il sera récupéré automatiquement dès que le serveur catalogue sera joignable.
            @endif
        </div>
    @else
        <input type="search" id="market-search" class="form-input market-search" placeholder="Rechercher un flux… (nom, description, catégorie)">

        <div class="market-chips" id="market-chips">
            <button type="button" class="chip active" data-category="">Tout</button>
            @foreach ($feeds->keys() as $category)
                <button type="button" class="chip" data-category="{{ $category }}">{{ $category }}</button>
            @endforeach
        </div>

        <div class="market-grid" id="market-grid">
            @foreach ($feeds as $category => $items)
                @foreach ($items as $item)
                    <div class="market-card" data-category="{{ $category }}" data-search="{{ Str::lower($item['title'].' '.$item['description'].' '.$category) }}">
                        <div class="market-card-head">
                            <span class="market-card-title">{{ $item['title'] }}</span>
                            <span class="chip chip-static">{{ $category }}</span>
                        </div>
                        <p class="market-card-desc">{{ $item['description'] ?: $item['url'] }}</p>
                        <div class="market-card-foot">
                            <span class="market-lang">{{ strtoupper($item['lang']) }}</span>
                            @if (in_array($item['url'], $subscribedUrls))
                                <span class="market-added">✓ Ajouté</span>
                            @else
                                <form method="POST" action="{{ route('feeds.store') }}">
                                    @csrf
                                    <input type="hidden" name="url" value="{{ $item['url'] }}">
                                    <input type="hidden" name="title" value="{{ $item['title'] }}">
                                    <button type="submit" class="btn btn-primary btn-sm">+ Ajouter</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

        <div class="card empty-state" id="market-empty" hidden>Aucun flux ne correspond à votre recherche.</div>
    @endif
</div>
@endsection

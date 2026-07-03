@extends('layouts.marketing', ['title' => 'BloomFeed — votre agrégateur RSS auto-hébergé'])

@section('content')
<section class="hero">
    <div class="hero-inner">
        <span class="eyebrow">Auto-hébergé &middot; Vie privée</span>
        <h1>Vos flux RSS, lus en profondeur, sans jamais quitter BloomFeed.</h1>
        <p class="hero-lede">
            BloomFeed centralise vos flux préférés et en extrait le contenu complet à la demande,
            directement sur votre propre serveur. Aucune donnée envoyée à des tiers.
        </p>
        <div class="hero-cta">
            @if ($hasOwner)
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Se connecter</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Créer un compte</a>
            @endif
            <a href="{{ route('self-hosting') }}" class="btn btn-ghost btn-lg">Comment l'auto-héberger ?</a>
        </div>
    </div>
</section>

<section class="feature-grid">
    <div class="feature-card">
        <div class="feature-icon">📰</div>
        <h3>Flux personnalisés</h3>
        <p>Ajoutez n'importe quel flux RSS ou Atom, organisez-les et suivez vos non-lus en un coup d'œil.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">📖</div>
        <h3>Lecture approfondie intégrée</h3>
        <p>BloomFeed récupère la page source, en extrait le contenu principal et l'affiche proprement — plus besoin d'ouvrir dix onglets.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🔒</div>
        <h3>Chez vous, pour vous</h3>
        <p>Une instance BloomFeed = un propriétaire. Pas d'inscriptions publiques, pas de tracking, votre serveur, vos données.</p>
    </div>
</section>
@endsection

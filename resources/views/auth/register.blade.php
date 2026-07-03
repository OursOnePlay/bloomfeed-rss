@extends('layouts.guest', ['title' => 'Créer un compte — BloomFeed'])

@section('content')
<div class="auth-card">
    <div class="logo-row">
        <img src="{{ asset('images/logo.png') }}" alt="BloomFeed" class="brand-logo brand-logo-light">
        <img src="{{ asset('images/logo-dark.png') }}" alt="BloomFeed" class="brand-logo brand-logo-dark">
    </div>
    <h1>Configurer votre instance</h1>
    <p class="subtitle">Ce compte sera le seul compte propriétaire de cette instance BloomFeed</p>

    @if ($errors->any())
        <div class="form-error mt-1">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-2">
        @csrf
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>
    </form>

    <div class="switch-link">
        Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a>
    </div>
</div>
@endsection

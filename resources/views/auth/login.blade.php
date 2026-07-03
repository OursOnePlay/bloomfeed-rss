@extends('layouts.guest', ['title' => 'Connexion — BloomFeed'])

@section('content')
<div class="auth-card">
    <div class="logo-row">
        <img src="{{ asset('images/logo.png') }}" alt="BloomFeed" class="brand-logo brand-logo-light">
        <img src="{{ asset('images/logo-dark.png') }}" alt="BloomFeed" class="brand-logo brand-logo-dark">
    </div>
    <h1>Connexion</h1>
    <p class="subtitle">Accédez à vos flux et à votre lecture approfondie</p>

    @if (session('status'))
        <div class="form-status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="form-error mt-1">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-2">
        @csrf
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
    </form>

    @unless (\App\Models\User::query()->exists())
        <div class="switch-link">
            Pas encore de compte ? <a href="{{ route('register') }}">Créer un compte</a>
        </div>
    @endunless
</div>
@endsection

@extends('layouts.guest', ['title' => __('app.login_title').' - BloomFeed'])

@section('content')
<div class="auth-card">
    <div class="logo-row">
        <img src="{{ url()->assetFrom(request()->getBaseUrl(), 'images/logo.png') }}" alt="BloomFeed" class="brand-logo brand-logo-light">
        <img src="{{ url()->assetFrom(request()->getBaseUrl(), 'images/logo-dark.png') }}" alt="BloomFeed" class="brand-logo brand-logo-dark">
    </div>
    <h1>{{ __('app.login_title') }}</h1>
    <p class="subtitle">{{ __('app.login_subtitle') }}</p>

    @if (session('status'))
        <div class="form-status" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div role="alert" class="form-error mt-1">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-2">
        @csrf
        <div class="form-group">
            <label for="email">{{ __('app.email') }}</label>
            <input type="email" autocomplete="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">{{ __('app.password') }}</label>
            <input type="password" autocomplete="current-password" name="password" id="password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">{{ __('app.login') }}</button>
    </form>

    @if (! \App\Models\User::query()->exists() || \App\Models\Setting::registrationsOpen())
        <div class="switch-link">
            {{ __('app.no_account_yet') }} <a href="{{ route('register') }}">{{ __('app.create_account') }}</a>
        </div>
    @endif
</div>
@endsection

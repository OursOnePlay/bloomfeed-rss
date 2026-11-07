@extends('layouts.guest', ['title' => __('app.create_account').' - BloomFeed'])

@php($isFirstUser = ! \App\Models\User::query()->exists())

@section('content')
<div class="auth-card">
    <div class="logo-row">
        <img src="{{ asset('images/logo.png') }}" alt="BloomFeed" class="brand-logo brand-logo-light">
        <img src="{{ asset('images/logo-dark.png') }}" alt="BloomFeed" class="brand-logo brand-logo-dark">
    </div>
    <h1>{{ $isFirstUser ? __('app.register_owner_title') : __('app.register_title') }}</h1>
    <p class="subtitle">{{ $isFirstUser ? __('app.register_owner_subtitle') : __('app.register_subtitle') }}</p>

    @if ($errors->any())
        <div role="alert" class="form-error mt-1">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-2">
        @csrf
        <div class="form-group">
            <label for="name">{{ __('app.name') }}</label>
            <input type="text" autocomplete="name" name="name" id="name" class="form-input" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="email">{{ __('app.email') }}</label>
            <input type="email" autocomplete="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label for="password">{{ __('app.password') }}</label>
            <input type="password" autocomplete="new-password" name="password" id="password" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">{{ __('app.confirm_password') }}</label>
            <input type="password" autocomplete="new-password" name="password_confirmation" id="password_confirmation" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">{{ __('app.register_button') }}</button>
    </form>

    <div class="switch-link">
        {{ __('app.already_account') }} <a href="{{ route('login') }}">{{ __('app.login') }}</a>
    </div>
</div>
@endsection

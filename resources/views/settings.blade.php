@extends('layouts.app', ['title' => __('app.settings').' - BloomFeed'])

@section('content')
<div class="main-column form-section">
    @if (session('status'))
        <div class="form-status" role="status">{{ session('status') }}</div>
    @endif

    <h1 style="margin:0;">{{ __('app.settings_title') }}</h1>

    <div class="card">
        <h3>{{ __('app.registrations_section') }}</h3>
        <p class="text-muted text-sm">{{ __('app.registrations_desc') }}</p>
        <div class="flex-between mt-2">
            <span class="text-sm" style="font-weight:600; color: {{ $registrationsOpen ? 'var(--green)' : 'var(--text-muted)' }};">
                {{ $registrationsOpen ? __('app.registrations_state_open') : __('app.registrations_state_closed') }}
            </span>
            <form method="POST" action="{{ route('settings.toggleRegistrations') }}">
                @csrf
                <button type="submit" class="btn {{ $registrationsOpen ? 'btn-danger' : 'btn-primary' }} btn-sm">
                    {{ $registrationsOpen ? __('app.close_registrations') : __('app.open_registrations') }}
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <h3>{{ __('app.create_user_section') }}</h3>
        <p class="text-muted text-sm">{{ __('app.create_user_desc') }}</p>

        @if ($errors->any())
            <div role="alert" class="form-error mt-1">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('settings.createUser') }}" class="mt-2">
            @csrf
            <div class="form-group">
                <label for="new-name">{{ __('app.name') }}</label>
                <input type="text" name="name" id="new-name" class="form-input" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="new-email">{{ __('app.email') }}</label>
                <input type="email" name="email" id="new-email" class="form-input" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="new-password">{{ __('app.password') }}</label>
                <div class="password-field"><input type="password" name="password" id="new-password" class="form-input" minlength="8" required autocomplete="new-password"><button type="button" class="btn btn-ghost" data-password-toggle="new-password" aria-pressed="false">{{ __('design.show_password') }}</button></div>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('app.create_account') }}</button>
        </form>
    </div>

    <div class="card">
        <h3>{{ __('app.catalog_sources_section') }}</h3>
        <p class="text-muted text-sm">{{ __('app.catalog_sources_desc') }}</p>

        @error('source_url')
            <div role="alert" class="form-error mt-1">{{ $message }}</div>
        @enderror

        @if (empty($catalogSources))
            <p class="text-muted text-sm mt-2">{{ __('app.no_sources') }}</p>
        @else
            <ul class="feed-list mt-2">
                @foreach ($catalogSources as $source)
                    <li style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:8px 10px;">
                        <span class="text-sm" style="overflow-wrap:anywhere;">{{ $source }}</span>
                        <form method="POST" action="{{ route('settings.removeSource') }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="source_url" value="{{ $source }}">
                            <button type="submit" class="btn btn-danger btn-sm">{{ __('app.remove') }}</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('settings.addSource') }}" class="mt-2">
            @csrf
            <div class="form-group">
                <label for="source-url">{{ __('app.source_url_label') }}</label>
                <input type="url" name="source_url" id="source-url" class="form-input" placeholder="https://git.example.com/user/repo/raw/branch/main/bloomflux.json" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">{{ __('app.add_source') }}</button>
        </form>
    </div>

    <div class="card">
        <h3>{{ __('app.users_section') }}</h3>
        <ul class="feed-list mt-1">
            @foreach ($users as $user)
                <li style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px;">
                    <span>
                        <strong>{{ $user->name }}</strong>
                        <span class="text-muted text-sm">&middot; {{ $user->email }}</span>
                    </span>
                    @if ($user->is_owner)
                        <span class="pill">{{ __('app.owner_badge') }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection

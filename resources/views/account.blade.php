@extends('layouts.app', ['title' => __('account.title').' - BloomFeed'])
@section('content')
<div class="main-column form-section">
    <h1>{{ __('account.title') }}</h1>
    @if (session('status'))<div class="form-status" role="status">{{ session('status') }}</div>@endif
    <section class="card">
        <h2>{{ __('account.change_password') }}</h2>
        <form method="POST" action="{{ route('account.password') }}">
            @csrf
            @method('PATCH')
            @foreach (['current_password' => 'current', 'password' => 'new', 'password_confirmation' => 'confirm'] as $field => $label)
                <div class="form-group">
                    <label for="{{ $field }}">{{ __('account.'.$label) }}</label>
                    <input class="form-input" type="password" id="{{ $field }}" name="{{ $field }}" autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}" @if ($field !== 'current_password') minlength="8" @endif required @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror>
                    @error($field)<p class="form-error" id="{{ $field }}-error" role="alert">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <p class="form-hint">{{ __('account.hint') }}</p>
            <button type="submit" class="btn btn-primary">{{ __('account.save') }}</button>
        </form>
    </section>
</div>
@endsection

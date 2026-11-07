@extends('layouts.app', ['title' => __('app.notifiers').' - BloomFeed'])

@section('content')
<div class="main-column form-section">
    @if (session('status'))
        <div class="form-status" role="status">{{ session('status') }}</div>
    @endif

    <h1 style="margin:0;">{{ __('app.notifiers') }}</h1>
    <p class="text-muted text-sm" style="margin:4px 0 0;">{{ __('app.notifiers_lede') }}</p>

    <details class="card mt-2" @if ($notifiers->isEmpty() || $errors->any()) open @endif>
        <summary class="section-summary">{{ __('app.new_notifier') }}</summary>

        @if ($errors->any())
            <div role="alert" class="form-error mt-1">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('notifiers.store') }}" class="notifier-form mt-2">
            @csrf
            <div class="form-group">
                <label for="new-type">{{ __('app.notifier_type') }}</label>
                <select name="type" id="new-type" class="form-input">
                    <option value="discord">Discord</option>
                    <option value="webhook">{{ __('app.generic_webhook') }}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="new-name">{{ __('app.notifier_name') }}</label>
                <input type="text" name="name" id="new-name" class="form-input" placeholder="{{ __('app.notifier_name_placeholder') }}" required>
            </div>
            <div class="form-group">
                <label for="new-url">{{ __('app.notifier_url') }}</label>
                <input type="url" name="url" id="new-url" class="form-input" placeholder="https://discord.com/api/webhooks/…" required>
            </div>
            <div class="form-group">
                <label for="new-scope">{{ __('app.notifier_scope') }}</label>
                <select name="scope" id="new-scope" class="form-input" data-scope-select>
                    <option value="all">{{ __('app.scope_all') }}</option>
                    <option value="feeds">{{ __('app.scope_feeds') }}</option>
                </select>
            </div>
            <div class="form-group notifier-feed-picker" data-scope-feeds hidden>
                <label>{{ __('app.scope_feeds_pick') }}</label>
                @forelse ($feeds as $feed)
                    <label class="notifier-feed-checkbox">
                        <input type="checkbox" name="feed_ids[]" value="{{ $feed->id }}">
                        {{ $feed->displayTitle() }}
                    </label>
                @empty
                    <p class="text-muted text-sm">{{ __('app.no_feeds_yet') }}</p>
                @endforelse
            </div>
            <button type="submit" class="btn btn-primary">{{ __('app.add_notifier') }}</button>
        </form>
    </details>

    <h3 class="mt-3">{{ __('app.your_notifiers') }}</h3>

    @forelse ($notifiers as $notifier)
        <div class="card notifier-card">
            <div class="flex-between">
                <div>
                    <span class="pill">{{ $notifier->type === 'discord' ? 'Discord' : __('app.generic_webhook') }}</span>
                    <strong style="margin-left:8px;">{{ $notifier->name }}</strong>
                    @unless ($notifier->enabled)
                        <span class="text-muted text-sm">({{ __('app.disabled') }})</span>
                    @endunless
                </div>
                <div class="flex" style="gap:8px;">
                    <form method="POST" action="{{ route('notifiers.test', $notifier) }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">{{ __('app.send_test') }}</button>
                    </form>
                    <form method="POST" action="{{ route('notifiers.toggle', $notifier) }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">{{ $notifier->enabled ? __('app.disable') : __('app.enable') }}</button>
                    </form>
                    <form method="POST" action="{{ route('notifiers.destroy', $notifier) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" data-confirm="{{ __('app.confirm_delete_notifier') }}">{{ __('app.delete') }}</button>
                    </form>
                </div>
            </div>

            <p class="text-muted text-sm mt-1">
                {{ $notifier->scope === 'all' ? __('app.scope_all') : __('app.scope_feeds_list', ['feeds' => $notifier->feeds->map->displayTitle()->join(', ') ?: __('app.no_feeds_selected')]) }}
                @if ($notifier->last_triggered_at)
                    &middot; {{ __('app.last_triggered', ['time' => $notifier->last_triggered_at->diffForHumans()]) }}
                @endif
                @if ($notifier->last_error)
                    &middot; <span style="color:var(--danger);">{{ $notifier->last_error }}</span>
                @endif
            </p>

            <details class="mt-1">
                <summary class="text-sm text-muted" style="cursor:pointer;">{{ __('app.edit') }}</summary>
                <form method="POST" action="{{ route('notifiers.update', $notifier) }}" class="notifier-form mt-2">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="notifier-{{ $notifier->id }}-type">{{ __('app.notifier_type') }}</label>
                        <select id="notifier-{{ $notifier->id }}-type" name="type" class="form-input">
                            <option value="discord" @selected($notifier->type === 'discord')>Discord</option>
                            <option value="webhook" @selected($notifier->type === 'webhook')>{{ __('app.generic_webhook') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notifier-{{ $notifier->id }}-name">{{ __('app.notifier_name') }}</label>
                        <input type="text" id="notifier-{{ $notifier->id }}-name" name="name" class="form-input" value="{{ $notifier->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="notifier-{{ $notifier->id }}-url">{{ __('app.notifier_url') }}</label>
                        <input type="url" id="notifier-{{ $notifier->id }}-url" name="url" class="form-input" value="{{ $notifier->url }}" required>
                    </div>
                    <div class="form-group">
                        <label for="notifier-{{ $notifier->id }}-scope">{{ __('app.notifier_scope') }}</label>
                        <select id="notifier-{{ $notifier->id }}-scope" name="scope" class="form-input" data-scope-select>
                            <option value="all" @selected($notifier->scope === 'all')>{{ __('app.scope_all') }}</option>
                            <option value="feeds" @selected($notifier->scope === 'feeds')>{{ __('app.scope_feeds') }}</option>
                        </select>
                    </div>
                    <div class="form-group notifier-feed-picker" data-scope-feeds @if ($notifier->scope !== 'feeds') hidden @endif>
                        <label>{{ __('app.scope_feeds_pick') }}</label>
                        @php($subscribed = $notifier->feeds->pluck('id')->all())
                        @foreach ($feeds as $feed)
                            <label class="notifier-feed-checkbox">
                                <input type="checkbox" name="feed_ids[]" value="{{ $feed->id }}" @checked(in_array($feed->id, $subscribed))>
                                {{ $feed->displayTitle() }}
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-ghost btn-sm">{{ __('app.save') }}</button>
                </form>
            </details>
        </div>
    @empty
        <div class="card empty-state">{{ __('app.no_notifiers_yet') }}</div>
    @endforelse
</div>
@endsection

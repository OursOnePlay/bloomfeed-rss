@extends('layouts.app', ['title' => __('app.my_feeds').' - BloomFeed'])

@section('content')
<div class="main-column" style="width:100%;">
    @if (session('status'))
        <div class="form-status" role="status">{{ session('status') }}</div>
    @endif

    <h1>{{ __('app.my_feeds') }}</h1>
    <div class="card">
        <h2>{{ __('app.add_feed') }}</h2>
        <form method="POST" action="{{ route('feeds.store') }}" class="inline-form">
            @csrf
            <div class="field"><label for="feed-url">{{ __('design.feed_url') }}</label><input id="feed-url" type="url" name="url" class="form-input" value="{{ old('url') }}" placeholder="{{ __('app.feed_url_placeholder') }}" required></div>
            <div class="field"><label for="feed-title">{{ __('app.custom_title_optional') }}</label><input id="feed-title" type="text" name="title" class="form-input" value="{{ old('title') }}"></div>
            <button type="submit" class="btn btn-primary">{{ __('app.add') }}</button>
        </form>
        @error('url')
            <div role="alert" class="form-error mt-1">{{ $message }}</div>
        @enderror
    </div>



    @forelse ($feeds as $feed)
        <div class="card flex-between">
            <div>
                <div style="font-weight:600;">{{ $feed->displayTitle() }}</div>
                <div class="text-sm text-muted feed-url">{{ $feed->url }}</div>
                <div class="text-sm text-muted mt-1">
                    {{ __('app.articles_count', ['count' => $feed->articles_count]) }}
                    @if ($feed->last_fetched_at)
                        &middot; {{ __('app.refreshed_ago', ['time' => $feed->last_fetched_at->diffForHumans()]) }}
                    @endif
                    @if ($feed->last_fetch_status && $feed->last_fetch_status !== 'ok')
                        &middot; <span style="color:var(--danger);">{{ $feed->last_fetch_status }}</span>
                    @endif
                </div>
            </div>
            <div class="flex" style="gap:8px;">
                <form method="POST" action="{{ route('feeds.refresh', $feed) }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">{{ __('app.refresh') }}</button>
                </form>
                <form method="POST" action="{{ route('feeds.destroy', $feed) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="{{ __('app.confirm_delete_feed') }}">{{ __('app.delete') }}</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card empty-state">{{ __('app.no_feeds_yet') }}</div>
    @endforelse
</div>
@endsection

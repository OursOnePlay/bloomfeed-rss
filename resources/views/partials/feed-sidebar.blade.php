<details class="sidebar-panel" data-feed-panel open>
    <summary class="sidebar-heading">{{ __('app.feeds_label') }}</summary>
    <div class="sidebar-body">
    <div class="sidebar-title">{{ __('app.my_feeds') }}</div>
    <ul class="feed-list">
        <li><a href="{{ route('dashboard', request()->only(['q', 'unread', 'favorite'])) }}" class="{{ !request('feed') ? 'active' : '' }}" @if (!request('feed')) aria-current="page" @endif>{{ __('app.all_articles') }}</a></li>
        @foreach ($feeds as $feed)
            <li><a href="{{ route('dashboard', array_merge(request()->only(['q', 'unread', 'favorite']), ['feed' => $feed->id])) }}" class="{{ request('feed') == $feed->id ? 'active' : '' }}" @if (request('feed') == $feed->id) aria-current="page" @endif>
                <span>{{ $feed->displayTitle() }}</span>
                @if ($feed->unread_count > 0)<span class="pill" aria-label="{{ __('design.unread_count', ['count' => $feed->unread_count]) }}">{{ $feed->unread_count }}</span>@endif
            </a></li>
        @endforeach
    </ul>
    <div class="sidebar-actions">
        <details @error('url') open @enderror>
            <summary>{{ __('app.add_feed') }}</summary>
            <form method="POST" action="{{ route('feeds.store') }}">@csrf
                <div class="form-group"><label for="sidebar-url">{{ __('design.feed_url') }}</label><input type="url" name="url" id="sidebar-url" class="form-input" placeholder="{{ __('app.feed_url_placeholder') }}" value="{{ old('url') }}" required></div>
                @error('url')<p class="form-error" role="alert">{{ $message }}</p>@enderror
                <button type="submit" class="btn btn-ghost btn-block">{{ __('app.add') }}</button>
            </form>
        </details>
        <form method="POST" action="{{ route('feeds.refreshAll') }}">@csrf<button type="submit" class="btn btn-ghost btn-block">{{ __('app.refresh_all') }}</button></form>
        <a href="{{ route('feeds.index') }}" class="text-sm text-muted">{{ __('app.manage_feeds') }}</a>
    </div>
    </div>
</details>

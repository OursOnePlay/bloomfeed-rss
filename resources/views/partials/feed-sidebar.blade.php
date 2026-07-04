<div class="card">
    <div class="sidebar-title">{{ __('app.feeds_label') }}</div>
    <ul class="feed-list">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ ! request('feed') ? 'active' : '' }}">
                {{ __('app.all_articles') }}
            </a>
        </li>
        @foreach ($feeds as $feed)
            <li>
                <a href="{{ route('dashboard', ['feed' => $feed->id]) }}" class="{{ request('feed') == $feed->id ? 'active' : '' }}">
                    <span>{{ $feed->displayTitle() }}</span>
                    @if ($feed->unread_count > 0)
                        <span class="pill">{{ $feed->unread_count }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('feeds.store') }}" class="mt-2">
        @csrf
        <div class="form-group">
            <label for="sidebar-url">{{ __('app.add_feed') }}</label>
            <input type="url" name="url" id="sidebar-url" class="form-input" placeholder="{{ __('app.feed_url_placeholder') }}" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-sm">{{ __('app.add') }}</button>
    </form>

    <form method="POST" action="{{ route('feeds.refreshAll') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-ghost btn-block btn-sm">{{ __('app.refresh_all') }}</button>
    </form>

    <a href="{{ route('feeds.index') }}" class="text-sm text-muted mt-1" style="display:block; text-align:center;">
        {{ __('app.manage_feeds') }}
    </a>
</div>

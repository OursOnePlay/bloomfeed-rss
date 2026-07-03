<div class="card">
    <div class="sidebar-title">Flux</div>
    <ul class="feed-list">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ ! request('feed') ? 'active' : '' }}">
                Tous les articles
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
            <label for="sidebar-url">Ajouter un flux</label>
            <input type="url" name="url" id="sidebar-url" class="form-input" placeholder="https://exemple.com/rss.xml" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-sm">Ajouter</button>
    </form>

    <form method="POST" action="{{ route('feeds.refreshAll') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-ghost btn-block btn-sm">Tout actualiser</button>
    </form>

    <a href="{{ route('feeds.index') }}" class="text-sm text-muted mt-1" style="display:block; text-align:center;">
        Gérer mes flux
    </a>
</div>

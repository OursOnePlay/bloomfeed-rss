<article class="article-card {{ $article->is_read ? 'is-read' : '' }}">
    <div class="article-card-top"><div>
        <a href="{{ route('articles.show', $article) }}" class="article-title">{{ $article->title }}</a>
        <div class="article-meta"><span>{{ $article->feed->displayTitle() }}</span>
            @if ($article->published_at)<time datetime="{{ $article->published_at->toIso8601String() }}">{{ $article->published_at->diffForHumans() }}</time>@endif
            @if ($article->is_read)<span class="read-state">{{ __('design.read') }}</span>@endif
        </div>
    </div>
    <form action="{{ route('articles.toggleFavorite', $article) }}" method="POST" class="favorite-form" data-favorite-form>@csrf
        <button type="submit" class="favorite-btn {{ $article->is_favorite ? 'is-favorite' : '' }}" aria-label="{{ __('design.favorite_toggle') }}" aria-pressed="{{ $article->is_favorite ? 'true' : 'false' }}" data-error="{{ __('design.action_error') }}"><svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="m12 3 2.8 5.7 6.3.9-4.6 4.5 1.1 6.3L12 17.4l-5.6 3 1.1-6.3L3 9.6l6.2-.9Z"/></svg></button>
    </form></div>
    @if ($article->excerpt)<p class="article-excerpt">{{ $article->excerpt }}</p>@endif
</article>

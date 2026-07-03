<article class="article-card {{ $article->is_read ? 'is-read' : '' }}">
    <div class="article-card-top">
        <div>
            <a href="{{ route('articles.show', $article) }}" class="article-title">
                {{ $article->title }}
            </a>
            <div class="article-meta">
                {{ $article->feed->displayTitle() }}
                @if ($article->published_at)
                    &middot; {{ $article->published_at->diffForHumans() }}
                @endif
            </div>
        </div>
        <button type="button" class="favorite-btn {{ $article->is_favorite ? 'is-favorite' : '' }}" data-favorite-url="{{ route('articles.toggleFavorite', $article) }}" title="Favori">
            ★
        </button>
    </div>

    @if ($article->excerpt)
        <p class="article-excerpt">{{ $article->excerpt }}</p>
    @endif
</article>

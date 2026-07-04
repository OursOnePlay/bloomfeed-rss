@extends('layouts.app', ['title' => __('app.articles').' — BloomFeed'])

@section('content')
<div class="sidebar">
    @include('partials.feed-sidebar')
</div>

<div class="main-column">
    @if (session('status'))
        <div class="form-status">{{ session('status') }}</div>
    @endif

    <div class="flex-between">
        <h2 style="margin:0;">{{ __('app.latest_articles') }}</h2>
        <div class="flex" style="gap:8px;">
            <a href="{{ route('dashboard', array_filter(['feed' => request('feed'), 'unread' => request('unread') ? null : 1])) }}"
               class="btn btn-ghost btn-sm">
                {{ request('unread') ? __('app.show_all') : __('app.unread_only') }}
            </a>
        </div>
    </div>

    @forelse ($articles as $article)
        @include('partials.article-card', ['article' => $article])
    @empty
        <div class="card empty-state">
            @if ($feeds->isEmpty())
                {{ __('app.empty_no_feeds') }}
            @else
                {{ __('app.empty_no_articles') }}
            @endif
        </div>
    @endforelse

    <div class="pagination">
        {{ $articles->links() }}
    </div>
</div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Services\FeedFetcherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(Request $request): View
    {
        $feeds = $request->user()->feeds()->withCount('articles')->orderBy('title')->get();

        return view('feeds.index', ['feeds' => $feeds]);
    }

    public function store(Request $request, FeedFetcherService $service): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $feed = $request->user()->feeds()->create([
            'url' => $validated['url'],
            'title' => $validated['title'] ?? null,
        ]);

        $result = $service->fetchFeed($feed);

        if ($result['error'] !== null) {
            return back()->with('status', __('app.feed_added_error', ['error' => $result['error']]));
        }

        return back()->with('status', __('app.feed_added', ['count' => $result['new']]));
    }

    public function destroy(Feed $feed): RedirectResponse
    {
        $this->authorize('delete', $feed);

        $feed->delete();

        return back()->with('status', __('app.feed_deleted'));
    }

    public function refresh(Feed $feed, FeedFetcherService $service): RedirectResponse
    {
        $this->authorize('update', $feed);

        $result = $service->fetchFeed($feed);

        if ($result['error'] !== null) {
            return back()->with('status', __('app.refresh_failed', ['error' => $result['error']]));
        }

        return back()->with('status', __('app.new_articles', ['count' => $result['new']]));
    }

    public function refreshAll(Request $request, FeedFetcherService $service): RedirectResponse
    {
        $feeds = $request->user()->feeds;
        $totalNew = 0;

        foreach ($feeds as $feed) {
            $totalNew += $service->fetchFeed($feed)['new'];
        }

        return back()->with('status', __('app.new_articles_across', ['count' => $totalNew, 'feeds' => $feeds->count()]));
    }
}

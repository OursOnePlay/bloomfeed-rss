<?php

namespace App\Http\Controllers;

use App\Jobs\RefreshFeed;
use App\Models\Feed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(Request $request): View
    {
        $feeds = $request->user()->feeds()->withCount('articles')->orderBy('title')->get();

        return view('feeds.index', ['feeds' => $feeds]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $feed = DB::transaction(function () use ($request, $validated) {
            $request->user()->newQuery()->whereKey($request->user()->id)->lockForUpdate()->firstOrFail();
            abort_if($request->user()->feeds()->count() >= config('security.max_feeds'), 422, 'Subscription limit reached.');

            return $request->user()->feeds()->create([
                'url' => $validated['url'],
                'title' => $validated['title'] ?? null,
            ]);
        });

        RefreshFeed::dispatch($feed->id);

        return back()->with('status', __('app.refresh_queued'));
    }

    public function destroy(Feed $feed): RedirectResponse
    {
        $this->authorize('delete', $feed);

        $feed->delete();

        return back()->with('status', __('app.feed_deleted'));
    }

    public function refresh(Feed $feed): RedirectResponse
    {
        $this->authorize('update', $feed);
        RefreshFeed::dispatch($feed->id);

        return back()->with('status', __('app.refresh_queued'));
    }

    public function refreshAll(Request $request): RedirectResponse
    {
        $request->user()->feeds()->select('id')->chunkById(100, function ($feeds) {
            foreach ($feeds as $feed) {
                RefreshFeed::dispatch($feed->id);
            }
        });

        return back()->with('status', __('app.refresh_queued'));
    }
}

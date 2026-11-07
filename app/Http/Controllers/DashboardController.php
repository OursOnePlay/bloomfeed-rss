<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:200']]);

        $user = $request->user();

        $query = Article::forUser($user->id)->with('feed');

        if ($request->boolean('unread')) {
            $query->unread();
        }

        if ($feedId = $request->integer('feed')) {
            $query->where('feed_id', $feedId);
        }

        if ($request->boolean('favorite')) {
            $query->where('is_favorite', true);
        }

        $search = mb_substr(trim((string) $request->input('q', '')), 0, 200);
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhereHas('feed', fn ($feed) => $feed->where('title', 'like', '%'.$search.'%')->orWhere('site_title', 'like', '%'.$search.'%'));
            });
        }

        $articles = $query->orderByDesc('published_at')->paginate(20)->withQueryString();

        $feeds = $user->feeds()->withCount(['articles as unread_count' => fn ($q) => $q->unread()])
            ->orderBy('title')
            ->get();

        return view('dashboard', [
            'articles' => $articles,
            'feeds' => $feeds,
            'selectedFeed' => $feeds->firstWhere('id', $request->integer('feed')),
        ]);
    }
}

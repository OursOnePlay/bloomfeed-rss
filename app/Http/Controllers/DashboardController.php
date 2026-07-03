<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Article::forUser($user->id)->with('feed');

        if ($request->boolean('unread')) {
            $query->unread();
        }

        if ($feedId = $request->integer('feed')) {
            $query->where('feed_id', $feedId);
        }

        $articles = $query->orderByDesc('published_at')->paginate(20)->withQueryString();

        $feeds = $user->feeds()->withCount(['articles as unread_count' => fn ($q) => $q->unread()])
            ->orderBy('title')
            ->get();

        return view('dashboard', [
            'articles' => $articles,
            'feeds' => $feeds,
        ]);
    }
}

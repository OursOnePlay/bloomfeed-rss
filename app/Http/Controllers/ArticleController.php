<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleReaderService;
use App\Support\MarkdownRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function show(Article $article, ArticleReaderService $reader): View
    {
        $this->authorize('view', $article);

        if (! $article->is_read) {
            $article->update(['is_read' => true]);
        }

        if ($article->full_content_markdown === null && $article->full_content_error === null) {
            $reader->fetchFullContent($article);
        }

        return view('articles.show', [
            'article' => $article,
            'contentHtml' => MarkdownRenderer::toHtml($article->full_content_markdown),
        ]);
    }

    public function retry(Article $article, ArticleReaderService $reader): RedirectResponse
    {
        $this->authorize('update', $article);

        $article->full_content_error = null;
        $article->save();
        $reader->fetchFullContent($article);

        return redirect()->route('articles.show', $article);
    }

    public function markRead(Article $article): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $article);

        $article->update(['is_read' => true]);

        return request()->wantsJson()
            ? response()->json(['is_read' => true])
            : back();
    }

    public function toggleFavorite(Article $article): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $article);

        $article->update(['is_favorite' => ! $article->is_favorite]);

        return request()->wantsJson()
            ? response()->json(['is_favorite' => $article->is_favorite])
            : back();
    }
}

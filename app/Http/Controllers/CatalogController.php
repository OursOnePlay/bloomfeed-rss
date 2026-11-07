<?php

namespace App\Http\Controllers;

use App\Services\FluxCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request, FluxCatalogService $catalog): View|JsonResponse
    {
        $subscribedUrls = $request->user()->feeds()->pluck('url')->all();

        $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'lang' => ['nullable', 'string', 'max:10'],
            'category' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);
        $all = collect($catalog->getCatalog());
        $query = mb_strtolower(trim((string) $request->input('q', '')));
        $filtered = $all->filter(function ($feed) use ($request, $query) {
            return (! $request->filled('lang') || $feed['lang'] === $request->input('lang'))
                && (! $request->filled('category') || $feed['category'] === $request->input('category'))
                && ($query === '' || str_contains(mb_strtolower($feed['title'].' '.$feed['description'].' '.$feed['category']), $query));
        })->values();
        $page = $request->integer('page', 1);
        $feeds = new LengthAwarePaginator($filtered->forPage($page, 24)->values(), $filtered->count(), 24, $page, [
            'path' => route('catalog'), 'query' => $request->except('page'),
        ]);
        $data = [
            'feeds' => $feeds,
            'subscribedUrls' => $subscribedUrls,
            'categories' => $all->pluck('category')->unique()->values(),
            'languages' => $all->pluck('lang')->unique()->sort()->values(),
            'hasCatalog' => $all->isNotEmpty(),
        ];
        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('partials.catalog-cards', $data)->render(),
                'next' => $feeds->nextPageUrl(),
                'total' => $feeds->total(),
            ]);
        }

        return view('catalog', $data);
    }
}

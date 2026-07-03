<?php

namespace App\Http\Controllers;

use App\Services\FluxCatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request, FluxCatalogService $catalog): View
    {
        $subscribedUrls = $request->user()->feeds()->pluck('url')->all();

        return view('catalog', [
            'feeds' => collect($catalog->getCatalog())->groupBy('category'),
            'subscribedUrls' => $subscribedUrls,
        ]);
    }
}

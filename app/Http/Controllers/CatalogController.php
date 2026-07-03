<?php

namespace App\Http\Controllers;

use App\Services\FluxCatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request, FluxCatalogService $catalog): View
    {
        $result = $catalog->getCatalog();

        $subscribedUrls = $request->user()->feeds()->pluck('url')->all();

        return view('catalog', [
            'feeds' => collect($result['feeds'])->groupBy('category'),
            'fromCache' => $result['fromCache'],
            'updatedAt' => $result['updatedAt'],
            'subscribedUrls' => $subscribedUrls,
        ]);
    }
}

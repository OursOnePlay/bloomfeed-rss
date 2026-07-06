<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('home', [
            'hasOwner' => User::query()->exists(),
        ]);
    }

    public function selfHosting(): View
    {
        return view('self-hosting');
    }

    /**
     * Allow/Disallow use paths, not absolute URLs, per the robots.txt spec.
     * Only the Sitemap directive takes an absolute URL, generated from this
     * instance's own domain so every self-hosted BloomFeed gets it for free.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Allow: /self-hosting',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /dashboard',
            'Disallow: /feeds',
            'Disallow: /catalog',
            'Disallow: /notifiers',
            'Disallow: /settings',
            'Disallow: /articles',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
    }

    public function sitemap(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('self-hosting'), 'priority' => '0.8'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($url['loc'])."</loc>\n";
            $xml .= '    <priority>'.$url['priority']."</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}

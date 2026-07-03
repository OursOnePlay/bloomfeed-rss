<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Feed;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use Throwable;

class FeedFetcherService
{
    /**
     * Fetch, parse and store new articles for a single feed.
     *
     * @return array{new: int, error: string|null}
     */
    public function fetchFeed(Feed $feed): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'BloomFeed/1.0 (+https://bloomfeed.local)'])
                ->get($feed->url);

            if ($response->failed()) {
                throw new RuntimeException("HTTP {$response->status()}");
            }

            $items = $this->parse($response->body(), $feed);
        } catch (Throwable $e) {
            $feed->last_fetch_status = 'error: '.Str::limit($e->getMessage(), 200, '');
            $feed->last_fetched_at = now();
            $feed->save();

            return ['new' => 0, 'error' => $e->getMessage()];
        }

        $newCount = 0;

        foreach ($items as $item) {
            $key = $item['guid'] !== '' ? $item['guid'] : $item['link'];

            if ($key === '') {
                continue;
            }

            $article = Article::firstOrNew([
                'feed_id' => $feed->id,
                'guid' => Str::limit($key, 512, ''),
            ]);

            if ($article->exists) {
                continue;
            }

            $article->fill([
                'title' => Str::limit($item['title'] !== '' ? $item['title'] : '(Sans titre)', 512, ''),
                'link' => Str::limit($item['link'], 2048, ''),
                'excerpt' => $item['excerpt'],
                'content' => $item['content'],
                'published_at' => $item['published_at'],
            ]);
            $article->feed()->associate($feed);
            $article->save();

            $newCount++;
        }

        $feed->last_fetched_at = now();
        $feed->last_fetch_status = 'ok';
        $feed->save();

        return ['new' => $newCount, 'error' => null];
    }

    /**
     * Parse an RSS 2.0 or Atom feed body into a normalized list of items.
     * May set $feed->site_title as a side effect (persisted by the caller).
     *
     * @return list<array{guid: string, title: string, link: string, excerpt: string, content: string, published_at: ?Carbon}>
     */
    private function parse(string $xml, Feed $feed): array
    {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml);
        libxml_clear_errors();

        if ($doc === false) {
            throw new RuntimeException('Flux XML invalide.');
        }

        $namespaces = $doc->getNamespaces(true);
        $items = [];

        if (isset($doc->channel)) {
            $channel = $doc->channel;

            if ($feed->site_title === null && isset($channel->title)) {
                $feed->site_title = Str::limit((string) $channel->title, 255, '');
            }

            foreach ($channel->item as $item) {
                $content = isset($namespaces['content']) ? $item->children($namespaces['content']) : null;
                $dc = isset($namespaces['dc']) ? $item->children($namespaces['dc']) : null;
                $description = (string) ($item->description ?? '');

                $items[] = [
                    'guid' => trim((string) ($item->guid ?? $item->link ?? '')),
                    'title' => trim((string) ($item->title ?? '')),
                    'link' => trim((string) ($item->link ?? '')),
                    'excerpt' => $this->cleanExcerpt($description),
                    'content' => ($content !== null && isset($content->encoded)) ? (string) $content->encoded : $description,
                    'published_at' => $this->parseDate((string) ($item->pubDate ?? ($dc->date ?? ''))),
                ];
            }
        } elseif ($doc->getName() === 'feed') {
            if ($feed->site_title === null && isset($doc->title)) {
                $feed->site_title = Str::limit((string) $doc->title, 255, '');
            }

            foreach ($doc->entry as $entry) {
                $link = $this->extractAtomLink($entry);
                $summary = (string) ($entry->summary ?? '');
                $content = (string) ($entry->content ?? '');
                $body = $content !== '' ? $content : $summary;

                $items[] = [
                    'guid' => trim((string) ($entry->id ?? $link)),
                    'title' => trim((string) ($entry->title ?? '')),
                    'link' => $link,
                    'excerpt' => $this->cleanExcerpt($summary !== '' ? $summary : $body),
                    'content' => $body,
                    'published_at' => $this->parseDate((string) ($entry->published ?? ($entry->updated ?? ''))),
                ];
            }
        } else {
            throw new RuntimeException('Format de flux non reconnu (ni RSS ni Atom).');
        }

        return $items;
    }

    private function extractAtomLink(SimpleXMLElement $entry): string
    {
        foreach ($entry->link as $link) {
            $attrs = $link->attributes();
            $rel = isset($attrs['rel']) ? (string) $attrs['rel'] : 'alternate';

            if ($rel === 'alternate') {
                return (string) ($attrs['href'] ?? '');
            }
        }

        $first = $entry->link[0] ?? null;

        return $first !== null ? (string) ($first->attributes()['href'] ?? '') : '';
    }

    private function cleanExcerpt(string $html): string
    {
        return Str::limit(trim(strip_tags($html)), 500);
    }

    private function parseDate(string $date): ?Carbon
    {
        if ($date === '') {
            return null;
        }

        try {
            return Carbon::parse($date);
        } catch (Throwable) {
            return null;
        }
    }
}

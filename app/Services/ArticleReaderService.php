<?php

namespace App\Services;

use App\Models\Article;
use fivefilters\Readability\Configuration as ReadabilityConfig;
use fivefilters\Readability\Readability;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;
use RuntimeException;
use Throwable;

class ArticleReaderService
{
    public function fetchFullContent(Article $article): void
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'BloomFeed/1.0 (+https://bloomfeed.local)'])
                ->get($article->link);

            if ($response->failed()) {
                throw new RuntimeException("HTTP {$response->status()}");
            }

            $config = new ReadabilityConfig();
            $config->setFixRelativeURLs(true);
            $config->setOriginalURL($article->link);
            $config->setSubstituteEntities(true);

            $readability = new Readability($config);
            $readability->parse($response->body());

            $extracted = $readability->getContent();

            if (! $extracted) {
                throw new RuntimeException("Impossible d'extraire le contenu de cette page.");
            }

            $converter = new HtmlConverter([
                'strip_tags' => true,
                'remove_nodes' => 'script style noscript iframe form nav footer',
                'hard_break' => true,
            ]);

            $markdown = trim($converter->convert($extracted));

            if ($markdown === '') {
                throw new RuntimeException('Le contenu extrait est vide.');
            }

            $article->full_content_markdown = $markdown;
            $article->full_content_fetched_at = now();
            $article->full_content_error = null;
            $article->save();
        } catch (Throwable $e) {
            $article->full_content_error = Str::limit($e->getMessage(), 250, '');
            $article->full_content_fetched_at = now();
            $article->save();
        }
    }
}

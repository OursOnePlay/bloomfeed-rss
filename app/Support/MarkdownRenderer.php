<?php

namespace App\Support;

use League\CommonMark\CommonMarkConverter;

class MarkdownRenderer
{
    public static function toHtml(?string $markdown): string
    {
        if (! $markdown) {
            return '';
        }

        static $converter;

        // html_input=strip: the source markdown comes from scraped third-party pages
        // (or LLM output echoing back scraped content), so raw HTML must never be
        // passed through as-is.
        $converter ??= new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);

        return (string) $converter->convert($markdown);
    }
}

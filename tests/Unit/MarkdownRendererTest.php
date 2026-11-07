<?php

namespace Tests\Unit;

use App\Support\MarkdownRenderer;
use PHPUnit\Framework\TestCase;

class MarkdownRendererTest extends TestCase
{
    public function test_scraped_markdown_cannot_emit_unsafe_links_or_html(): void
    {
        $html = MarkdownRenderer::toHtml('[Unsafe](javascript:alert%281%29) <script>alert(1)</script>');
        $this->assertStringNotContainsString('href="javascript:', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('href="https://example.com"', MarkdownRenderer::toHtml('[Source](https://example.com)'));
    }
}

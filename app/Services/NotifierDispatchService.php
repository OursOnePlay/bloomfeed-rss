<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Notifier;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class NotifierDispatchService
{
    /**
     * Notify every enabled notifier of the article's owner that matches this
     * article's feed (scope "all", or scope "feeds" including this feed).
     */
    public function notifyNewArticle(Article $article): void
    {
        $feed = $article->feed;

        $notifiers = Notifier::query()
            ->where('user_id', $feed->user_id)
            ->enabled()
            ->forFeed($feed->id)
            ->get();

        foreach ($notifiers as $notifier) {
            $this->send($notifier, $article);
        }
    }

    public function sendTest(Notifier $notifier): bool
    {
        $payload = $notifier->type === Notifier::TYPE_DISCORD
            ? $this->discordTestPayload()
            : $this->webhookTestPayload();

        return $this->deliver($notifier, $payload);
    }

    private function send(Notifier $notifier, Article $article): void
    {
        $payload = $notifier->type === Notifier::TYPE_DISCORD
            ? $this->discordPayload($notifier, $article)
            : $this->webhookPayload($notifier, $article);

        $this->deliver($notifier, $payload);
    }

    private function deliver(Notifier $notifier, array $payload): bool
    {
        try {
            $response = Http::timeout(6)->post($notifier->url, $payload);

            if ($response->failed()) {
                $notifier->update([
                    'last_error' => Str::limit("HTTP {$response->status()}", 250, ''),
                ]);

                return false;
            }

            $notifier->update(['last_triggered_at' => now(), 'last_error' => null]);

            return true;
        } catch (Throwable $e) {
            Log::warning('Notifier delivery failed', [
                'notifier_id' => $notifier->id,
                'error' => $e->getMessage(),
            ]);

            $notifier->update(['last_error' => Str::limit($e->getMessage(), 250, '')]);

            return false;
        }
    }

    private function discordPayload(Notifier $notifier, Article $article): array
    {
        return [
            'embeds' => [[
                'title' => Str::limit($article->title, 250),
                'url' => $article->link,
                'description' => $article->excerpt ? Str::limit($article->excerpt, 300) : null,
                'color' => 15768330, // amber accent
                'footer' => ['text' => $article->feed->displayTitle()],
                'timestamp' => $article->published_at?->toIso8601String() ?? now()->toIso8601String(),
            ]],
        ];
    }

    private function webhookPayload(Notifier $notifier, Article $article): array
    {
        return [
            'event' => 'article.created',
            'feed' => $article->feed->displayTitle(),
            'article' => [
                'title' => $article->title,
                'url' => $article->link,
                'excerpt' => $article->excerpt,
                'published_at' => $article->published_at?->toIso8601String(),
            ],
        ];
    }

    private function discordTestPayload(): array
    {
        return ['content' => '🔔 BloomFeed test notification — your Discord webhook is correctly configured.'];
    }

    private function webhookTestPayload(): array
    {
        return ['event' => 'test', 'message' => 'BloomFeed test notification — your webhook is correctly configured.'];
    }
}

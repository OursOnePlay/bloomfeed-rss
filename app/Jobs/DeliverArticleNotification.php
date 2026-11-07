<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Notifier;
use App\Services\NotifierDispatchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeliverArticleNotification implements ShouldQueue
{
    use Queueable;

    public int $timeout = 25;

    public int $tries = 1;

    public function __construct(public int $notifierId, public int $articleId) {}

    public function handle(NotifierDispatchService $dispatcher): void
    {
        $article = Article::with('feed')->find($this->articleId);
        $notifier = Notifier::enabled()->find($this->notifierId);
        if ($article && $notifier && $article->feed->user_id === $notifier->user_id
            && Notifier::whereKey($notifier->id)->forFeed($article->feed_id)->exists()) {
            $dispatcher->send($notifier, $article);
        }
    }
}

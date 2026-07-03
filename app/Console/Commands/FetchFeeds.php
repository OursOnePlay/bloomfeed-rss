<?php

namespace App\Console\Commands;

use App\Models\Feed;
use App\Services\FeedFetcherService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('feeds:fetch {--feed=* : Limit to specific feed ID(s)} {--user=* : Limit to a user\'s feeds}')]
#[Description('Fetch RSS/Atom feeds and store new articles')]
class FetchFeeds extends Command
{
    public function handle(FeedFetcherService $service): int
    {
        $query = Feed::query();

        if ($feedIds = $this->option('feed')) {
            $query->whereIn('id', $feedIds);
        } elseif ($userIds = $this->option('user')) {
            $query->whereIn('user_id', $userIds);
        }

        $feeds = $query->get();
        $totalNew = 0;

        foreach ($feeds as $feed) {
            try {
                $result = $service->fetchFeed($feed);
                $totalNew += $result['new'];

                if ($result['error'] !== null) {
                    $this->warn("[{$feed->id}] {$feed->url} : {$result['error']}");
                } else {
                    $this->info("[{$feed->id}] {$feed->url} : {$result['new']} nouvel(aux) article(s)");
                }
            } catch (Throwable $e) {
                $this->error("[{$feed->id}] {$feed->url} : {$e->getMessage()}");
            }
        }

        Log::info('feeds:fetch completed', ['feeds' => $feeds->count(), 'new_articles' => $totalNew]);

        return self::SUCCESS;
    }
}

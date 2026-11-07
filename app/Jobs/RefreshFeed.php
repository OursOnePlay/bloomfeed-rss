<?php

namespace App\Jobs;

use App\Models\Feed;
use App\Services\FeedFetcherService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RefreshFeed implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 900;

    public int $timeout = 90;

    public int $tries = 1;

    public function __construct(public int $feedId) {}

    public function uniqueId(): string
    {
        return 'feed:'.$this->feedId;
    }

    public function handle(FeedFetcherService $fetcher): void
    {
        if ($feed = Feed::find($this->feedId)) {
            $fetcher->fetchFeed($feed);
        }
    }
}

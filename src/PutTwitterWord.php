<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch;

use Juve534\TwitterSearch\Services\NotificationServiceInterface;
use Juve534\TwitterSearch\Services\TwitterService as Twitter;
use Monolog\Logger;

class PutTwitterWord
{
    public function __construct(
        private Twitter $client,
        private NotificationServiceInterface $notification,
        private Logger $logger
    )
    {}

    public function execute() : int
    {
        // TwitterAPIを実行
        $search = getenv('TWITTER_SEARCH_WORD');
        $this->logger->info("searchWord", [$search]);
        $tweets = $this->client->getTweets($search);

        // 集計時間から5分以内の呟きのみカウントする
        $count = 0;
        $targetEnd = time();
        $targetBegin = $targetEnd - 300;
        foreach ($tweets->statuses AS $tweet) {
            if ($targetBegin < strtotime($tweet->created_at)
                && strtotime($tweet->created_at) < $targetEnd
            ) {
                $count++;
            }
        }

        $this->notification->sendMessage($count);

        return $count;
    }
}

<?php

declare(strict_types=1);

use Juve534\TwitterSearch\TwitterService;
use Juve534\TwitterSearch\NotificationServiceInterface;

/**
 * Twitterで検索した結果、出て来る画像をランダムでSlackに通知する
 * Class TwitterImgSearch
 */
class TwitterImgSearch
{
    private TwitterService $client;
    private NotificationServiceInterface $notification;

    public function __construct(TwitterService $client, NotificationServiceInterface $notification)
    {
        $this->client = $client;
        $this->notification = $notification;
    }

    public function execute() : string|bool
    {
        // TwitterAPIを実行
        $search = $this->getTwitterSearchWord();
        $tweets = $this->client->getTweets($search);

        if (!$tweets || !property_exists($tweets, 'statuses')) {
            echo 'No Tweet' . PHP_EOL;
            return false;
        }

        $imageList = [];
        foreach ($tweets->statuses AS $tweet) {
            if (!property_exists($tweet, 'extended_entities')
                || !property_exists($tweet->extended_entities, 'media')
            ) {
                continue;
            }

            foreach ($tweet->extended_entities->media AS $media) {
                if (!property_exists($media, 'media_url_https')) {
                    continue;
                }
                if (empty($media->media_url_https)) {
                    continue;
                }

                $imageList[] = $media->media_url_https;
            }
        }

        $imageUrl = $imageList[rand(0, (count($imageList)))];

        $this->notification->sendMessage($imageUrl);

        return $imageUrl;
    }

    /**
     * 環境変数から検索を実行する文字を取得する
     *
     * @return string 検索文字列
     */
    private function getTwitterSearchWord() : string
    {
        $list = explode(',', getenv('TWITTER_SEARCH_IMG'));
        return $list[rand(0, (count($list) - 1))];
    }
}
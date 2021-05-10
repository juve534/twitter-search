<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch;

use Juve534\TwitterSearch\Services\DynamoDBService;
use Juve534\TwitterSearch\Services\MessageInterface;
use Juve534\TwitterSearch\Services\TwitterService;
use Juve534\TwitterSearch\Services\NotificationServiceInterface;
use Monolog\Logger;

/**
 * Twitterで検索した結果、出て来る画像をランダムでSlackに通知する
 * Class TwitterImgSearch
 */
class TwitterImgSearch
{
    public function __construct(
        private TwitterService $client,
        private NotificationServiceInterface $notification,
        private Logger $logger,
        private MessageInterface $message,
        private DynamoDBService $DBService
    )
    {}

    public function execute() : string|bool
    {
        // TwitterAPIを実行
        $search = $this->getTwitterSearchWord();
        $this->logger->info('searchWord', [$search]);
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

        $maxCount = (count($imageList) - 1);
        $imageUrl = $imageList[rand(0, $maxCount)];

        $this->logger->info('imageList', $imageList);

        $this->message->sendMessage(['word' => $search]);

        $this->notification->sendMessage($imageUrl);

        return $imageUrl;
    }

    /**
     * 環境変数から検索を実行する文字を取得する.
     * 前回の単語は対象から削除する.
     *
     * @return string 検索文字列
     */
    private function getTwitterSearchWord() : string
    {
        $record = $this->DBService->getItem([
            'type' => 'searchWords',
        ]);
        $words = (isset($record['words'])) ? $record['words'] : explode(',', getenv('TWITTER_SEARCH_IMG'));

        $record = $this->DBService->getItem([
            'type' => 'lastWord',
        ]);
        $lastWord = (isset($record['word'])) ? $record['word'] : '';
        $this->logger->info('lastWord', [$lastWord]);

        $list = [];
        foreach ($words AS $word) {
            if ($word === $lastWord) {
                continue;
            }

            $list[] = $word;
        }
        $this->logger->info('wordList', $list);
        
        $maxCount = (count($list) - 1);
        return $list[rand(0, $maxCount)];
    }
}
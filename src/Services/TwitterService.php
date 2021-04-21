<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch\Services;

use \Abraham\TwitterOAuth\TwitterOAuth;

class TwitterService
{
    public function __construct(private TwitterOAuth $twitterOAuth)
    {}

    /**
     * Twitterクライアント生成
     *
     * @return TwitterService
     */
    public static function create() : TwitterService
    {
        $consumerKey       = getenv('TWITTER_CONSUMER_KEY');
        $consumerSecret    = getenv('TWITTER_CONSUMER_SECRET');
        $accessToken       = getenv('TWITTER_ACCESS_TOKEN');
        $accessTokenSecret = getenv('TWITTER_ACCESS_TOKEN_SECRET');

        $twitterOAuth = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
        return new self($twitterOAuth);
    }

    /**
     * 文章からTwitter投稿内容を取得する
     *
     * @param string $search 検索を実行する文章
     * @param int $count 取得する件数
     * @return object 投稿内容
     */
    public function getTweets(string $search, int $count = 100) : object
    {
        // TwitterAPIを実行
        $params = [
            'q'     => $search,
            'count' => $count,
        ];
        return $this->twitterOAuth->get('search/tweets', $params);
    }
}
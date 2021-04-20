<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch;

use \Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{
    /**
     * @var TwitterOAuth
     */
    private $_twitterClient;

    public function __construct()
    {
        $this->initTwitterClient();
    }

    /**
     * Twitterクライアント生成
     *
     * @return bool
     */
    private function initTwitterClient() : bool
    {
        $consumerKey       = getenv('TWITTER_CONSUMER_KEY');
        $consumerSecret    = getenv('TWITTER_CONSUMER_SECRET');
        $accessToken       = getenv('TWITTER_ACCESS_TOKEN');
        $accessTokenSecret = getenv('TWITTER_ACCESS_TOKEN_SECRET');

        $this->_twitterClient = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
        return true;
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
        return $this->_twitterClient->get('search/tweets', $params);
    }
}
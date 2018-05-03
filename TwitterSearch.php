<?php
use \Mackerel\Client;

class TwitterSearch
{
    /**
     * @var Twitter
     */
    private $_twitterClient;

    /**
     * @var Mackerel\Client
     */
    private $_mackerel;

    public function __construct()
    {
        $this->_twitterClient = new Twitter();
    }

    public function execute() : int
    {
        // TwitterAPIを実行
        $search = getenv('TWITTER_SEARCH_WORD');
        $tweets = $this->_twitterClient->getTweets($search);

        // 集計時間から5分以内の呟きのみカウントする
        $count = 0;
        $targetEnd   = time();
        $targetBegin = $targetEnd - 300;
        foreach ($tweets->statuses AS $tweet) {
            if ($targetBegin < strtotime($tweet->created_at)
                && strtotime($tweet->created_at) < $targetEnd
            ) {
                $count++;
            }
        }

        // Mackerelにカウント数を登録
        $this->initMackerelClient();
        $hostId = getenv('MACKEREL_HOST_ID');
        $host   = $this->_mackerel->getHost($hostId);
        $metric = [
            'hostId' => $host->id,
            'time' => time(),
            'name' => getenv('CUSTOM_METRIC_TWITTER'),
            'value' => $count,
        ];
        $this->_mackerel->postMetrics([$metric]);

        return $count;
    }

    private function initMackerelClient()
    {
        $this->_mackerel = new Mackerel\Client([
            'mackerel_api_key' => getenv('MACKEREL_API_KEY'),
        ]);
        return true;
    }
}

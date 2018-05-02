<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';
use \Abraham\TwitterOAuth\TwitterOAuth;
use \Mackerel\Client;

class TwitterSearch
{
    /**
     * @var TwitterOAuth
     */
    private $_twitterClient;

    /**
     * @var Mackerel\Client
     */
    private $_mackerel;

    public function __construct()
    {
        $dotEnv = new Dotenv\Dotenv(__DIR__);
        $dotEnv->load();
    }

    public function execute() : int
    {
        $this->initTwitterClient();

        // TwitterAPIを実行
        $search = getenv('TWITTER_SEARCH_WORD');
        $params = [
            'q'     => $search,
            'count' => 100,
        ];
        $tweets = $this->_twitterClient->get('search/tweets', $params);

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

    private function initMackerelClient()
    {
        $this->_mackerel = new Mackerel\Client([
            'mackerel_api_key' => getenv('MACKEREL_API_KEY'),
        ]);
        return true;
    }
}
$timeZone = new \DateTimeZone('Asia/Tokyo');
echo (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;

$obj   = new TwitterSearch();
$count = $obj->execute();

echo $count . PHP_EOL;
echo (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;

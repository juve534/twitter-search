<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/vendor/autoload.php';

use Juve534\TwitterSearch;
use Juve534\TwitterSearch\TwitterImgSearch;
use \GuzzleHttp\Client;

if (empty($argv[1]) || !is_string($argv[1])) {
    echo 'STOP ' . PHP_EOL;
    exit;
}

// .envから環境変数を読み込み
$dotEnv = new Dotenv\Dotenv(__DIR__);
$dotEnv->load();

$guzzle = new Client();
$slack = new TwitterSearch\Services\SlackService($guzzle);
try {
    // バッチ処理の実行
    $timeZone = new \DateTimeZone('Asia/Tokyo');
    echo 'START : ' . (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;

    $twitterService = TwitterSearch\Services\TwitterService::create();
    $command  = $argv[1];
    switch ($command) {
        case 'TwitterImgSearch':
            $obj = new TwitterImgSearch($twitterService, $slack);
            break;
        case 'TwitterSearch':
            $mackerelService = new TwitterSearch\Services\MackerelService(new Mackerel\Client([
                'mackerel_api_key' => getenv('MACKEREL_API_KEY'),
            ]));
            $obj = new TwitterSearch\TwitterSearch($twitterService, $mackerelService);
            break;
        default:
            throw new RuntimeException('Not Found Class : ' . $command);
            break;
    }

    $result = $obj->execute();

    echo $result . PHP_EOL;
    echo 'END : ' . (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;
} catch (\Abraham\TwitterOAuth\TwitterOAuthException $e) {
    $message = 'Fail : ' . $command . PHP_EOL;
    $message .= (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;
    $message .= $e->getMessage() . PHP_EOL;

    $slack->sendMessage($message);
}
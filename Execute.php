<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/vendor/autoload.php';

use Juve534\TwitterSearch;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Aws\DynamoDb\DynamoDbClient;
use Juve534\TwitterSearch\Services\DynamoDBService;

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

    $log = new Logger($command);
    $handler = new StreamHandler('php://stdout', Logger::DEBUG);
    $log->pushHandler($handler);

    switch ($command) {
        case 'TwitterImgSearch':
            $queueUrl = getenv('TWITTER_SEARCH_QUE_URL');
            $messageService = new TwitterSearch\Services\SqsMessageService(new \Aws\Sqs\SqsClient([
                'region' => 'ap-northeast-1',
                'version' => '2012-11-05'
            ]), $queueUrl);
            $obj = new TwitterSearch\TwitterImgSearch($twitterService, $slack, $log, $messageService, new DynamoDBService(new DynamoDbClient([
                'region'   => 'ap-northeast-1', //使用するregion情報
                'version'  => 'latest'
            ])));
            break;
        case 'PutTwitterWord':
            $mackerelService = new TwitterSearch\Services\MackerelService(new Mackerel\Client([
                'mackerel_api_key' => getenv('MACKEREL_API_KEY'),
            ]));
            $obj = new TwitterSearch\PutTwitterWord($twitterService, $mackerelService, $log);
            break;
        default:
            throw new RuntimeException('Not Found Class : ' . $command);
            break;
    }

    $result = $obj->execute();

    echo $result . PHP_EOL;
    echo 'END : ' . (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;
} catch (\Exception $e) {
    $message = 'Fail : ' . $command . PHP_EOL;
    $message .= (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;
    $message .= $e->getMessage() . PHP_EOL;

    $slack->sendMessage($message);
}
<?php
declare(strict_types=1);

require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/class/Twitter.php';
require_once dirname(__FILE__) . '/class/Slack.php';

if (empty($argv[1]) || !is_string($argv[1])) {
    echo 'STOP ' . PHP_EOL;
    exit;
}

// .envから環境変数を読み込み
$dotEnv = new Dotenv\Dotenv(__DIR__);
$dotEnv->load();

$command  = $argv[1];
$fileName = $command . '.php';
if (!file_exists($fileName)) {
    throw new RuntimeException('Not Found File : ' . $fileName);
}

require_once dirname(__FILE__) . '/' . $command . '.php';

if (!class_exists($command)) {
    throw new RuntimeException('Not Found Class : ' . $command);
}

try {
    // バッチ処理の実行
    $timeZone = new \DateTimeZone('Asia/Tokyo');
    echo 'START : ' . (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;

    $obj    = new $command();
    $result = $obj->execute();

    echo $result . PHP_EOL;
    echo 'END : ' . (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;
} catch (\Abraham\TwitterOAuth\TwitterOAuthException $e) {
    $message = 'Fail : ' . $command . PHP_EOL;
    $message .= (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;
    $message .= $e->getMessage() . PHP_EOL;

    $slack = new Slack();
    $slack->sendToSlack($message);
}
<?php
declare(strict_types=1);
require_once dirname(__FILE__) . '/vendor/autoload.php';
require_once dirname(__FILE__) . '/class/Twitter.php';

if (empty($argv[1]) || !is_string($argv[1])) {
    echo 'STOP ' . PHP_EOL;
    exit;
}

// .envから環境変数を読み込み
$dotEnv = new Dotenv\Dotenv(__DIR__);
$dotEnv->load();

$command = $argv[1];

require_once dirname(__FILE__) . '/' . $command . '.php';

if (!class_exists($command)) {
    throw new RuntimeException('Not Found Class : ' . $command);
}

// バッチ処理の実行
$timeZone = new \DateTimeZone('Asia/Tokyo');
echo 'START : ' . (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;

$obj   = new $command();
$result = $obj->execute();

echo $result . PHP_EOL;
echo 'END : ' . (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s') . PHP_EOL;
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Aws\DynamoDb\DynamoDbClient;
use Juve534\TwitterSearch\Services\DynamoDBService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Juve534\TwitterSearch\PutLastWordCommand;

// .envから環境変数を読み込み
$dotEnv = new Dotenv\Dotenv(__DIR__);
$dotEnv->load();

$logger = new Logger('worker');
$handler = new StreamHandler('php://stdout', Logger::DEBUG);
$logger->pushHandler($handler);

$sdk = new DynamoDbClient([
    'region'   => 'ap-northeast-1',
    'version'  => 'latest'
]);

return new PutLastWordCommand(new DynamoDBService($sdk), $logger);
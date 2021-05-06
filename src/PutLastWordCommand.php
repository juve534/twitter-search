<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch;

use Bref\Context\Context;
use Bref\Event\Sqs\SqsEvent;
use Bref\Event\Sqs\SqsHandler;
use Juve534\TwitterSearch\Services\DynamoDBService;
use Monolog\Logger;

class PutLastWordCommand extends SqsHandler
{
    public function __construct(
        private DynamoDBService $service,
        private Logger $logger
    )
    {}

    public function handleSqs(SqsEvent $event, Context $context): void
    {
        foreach ($event->getRecords() as $record) {
            $body = $record->getBody();

            $this->logger->info("message-body", \json_decode($body, associative:true));

            $tmp = \json_decode($body, associative:true);

            $item = [
                'type' => 'lastWord',
                'word' => $tmp['word'],
            ];

            try {
                $this->service->putItem(\json_encode($item));
            } catch (\Aws\DynamoDb\Exception\DynamoDbException $e) {
                $this->logger->error($e->getMessage(), [$e]);
            }
        }
    }
}
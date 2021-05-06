<?php

declare(strict_types=1);

namespace Tests\Services;


use Aws\MockHandler;
use Aws\Result;
use Aws\Sqs\SqsClient;
use Juve534\TwitterSearch\Services\SqsMessageService;
use PHPUnit\Framework\TestCase;

class SqsMessageServiceTest extends TestCase
{
    private array $awsParam = [
        'region' => 'ap-northeast-1',
        'version' => '2012-11-05',
        'credentials' => false,
    ];

    /**
     * @test
     */
    public function メッセージ送信成功()
    {
        $queueUrl = 'hoge';
        $message = [
            'name' => 'taro',
            'message' => 'hello',
        ];

        $mock = new MockHandler();
        $mock->append(new Result(['foo' => 'bar']));

        $this->awsParam['handler'] = $mock;

        $stubClass = new SqsMessageService(new SqsClient($this->awsParam), $queueUrl);
        $stubClass->sendMessage($message);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function メッセージ受信成功()
    {
        $queueUrl = 'hoge';
        $message = [
            'name' => 'taro',
            'message' => 'hello',
        ];

        $mock = new MockHandler();
        $mock->append(new Result(
            [
                'Messages' => [
                    [
                        'Body' => \json_encode($message)
                    ],
                ],
            ]
        ));
        $this->awsParam['handler'] = $mock;

        $stubClass = new SqsMessageService(new SqsClient($this->awsParam), $queueUrl);
        $actual = $stubClass->receiveMessage();

        $this->assertSame($message, $actual);
    }
}
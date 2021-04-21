<?php

declare(strict_types=1);

namespace Tests\Services;

use Mackerel\Client;
use Juve534\TwitterSearch\Services\MackerelService;
use PHPUnit\Framework\TestCase;

class MackerelServiceTest extends TestCase
{
    const MACKEREL_HOST_ID = 'test';

    protected function setUp(): void
    {
        parent::setUp();

        putenv('MACKEREL_HOST_ID=' . self::MACKEREL_HOST_ID);
    }

    /**
     * @test
     */
    public function メッセージ送信成功()
    {
        // setup
        $message = 123;
        $mock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['getHost', 'postMetrics'])
            ->getMock();
        $mock->expects($this->once())
            ->method('getHost')
            ->with()
            ->willReturn(new class(){
                public $id = 'hoge';
            });
        $mock->expects($this->once())
            ->method('postMetrics');

        $obj = new MackerelService($mock);
        $obj->sendMessage($message);

        $this->assertTrue(true);
    }
}
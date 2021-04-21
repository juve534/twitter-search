<?php

declare(strict_types=1);

namespace Tests\Services;

use GuzzleHttp\Client;
use Juve534\TwitterSearch\Services\SlackService;
use PHPUnit\Framework\TestCase;

class SlackServiceTest extends TestCase
{
    const WEB_HOOK_URL = 'test';

    protected function setUp(): void
    {
        parent::setUp();

        putenv('WEB_HOOK_URL=' . self::WEB_HOOK_URL);
    }

    /**
     * @test
     */
    public function メッセージ送信成功()
    {
        // setup
        $message = 'hoge';
        $options = [
            'json' => [
                'username' => 'Bot',
                'text' => $message,
            ],
        ];
        $mock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();
        $mock->expects($this->once())
            ->method('post')
            ->with(self::WEB_HOOK_URL, $options);

        $obj = new SlackService($mock);
        $obj->sendMessage($message);

        $this->assertTrue(true);
    }
}
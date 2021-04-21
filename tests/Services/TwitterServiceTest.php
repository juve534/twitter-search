<?php

declare(strict_types=1);

namespace Tests\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use Juve534\TwitterSearch\Services\TwitterService;
use PHPUnit\Framework\TestCase;

class TwitterServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function ツイート取得()
    {
        // setup
        $search = 'hoge';
        $count = 1;
        $options = [
            'q'     => $search,
            'count' => $count,
        ];
        $expected = new class() {};
        $mock = $this->getMockBuilder(TwitterOAuth::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $mock->expects($this->once())
            ->method('get')
            ->with('search/tweets', $options)
            ->willReturn($expected);

        $obj = new TwitterService($mock);
        $actaul = $obj->getTweets($search, $count);

        $this->assertEquals($expected, $actaul);
    }
}
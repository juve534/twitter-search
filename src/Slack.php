<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch;

use \GuzzleHttp\Client;

/**
 * Class Slack
 */
class Slack
{
    /**
     * @var Client
     */
    private $client;

    private $webHookUrl;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->webHookUrl = getenv('WEB_HOOK_URL');
    }

    public function sendToSlack(string $text)
    {
        $uri = $this->webHookUrl;
        $options = [
            'json' => [
                'username' => 'Bot',
                'text' => $text,
            ],
        ];
        $response = $this->client->post($uri, $options);
        return $response;
    }

    /**
     * Slack通知先をデフォルトから変更する
     *
     * @param string $url webhookのURL
     * @return bool true
     */
    public function setWebHookUrl(string $url) : bool
    {
        if (strpos($url, 'https') !== false) {
            throw new LogicException('Invalid Url : ' . var_export($url, true));
        }
        $this->webHookUrl = $url;
        return true;
    }
}
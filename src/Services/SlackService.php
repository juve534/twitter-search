<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch;

use \GuzzleHttp\Client;

/**
 * Class Slack
 */
class SlackService implements NotificationServiceInterface
{
    /** @see Client */
    private Client $client;

    private string $webHookUrl;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->webHookUrl = getenv('WEB_HOOK_URL');
    }

    public function sendMessage(string $text): void
    {
        $uri = $this->webHookUrl;
        $options = [
            'json' => [
                'username' => 'Bot',
                'text' => $text,
            ],
        ];
        $this->client->post($uri, $options);
    }

    /**
     * Slack通知先をデフォルトから変更する
     *
     * @param string $url webhookのURL
     */
    public function setWebHookUrl(string $url) : void
    {
        if (strpos($url, 'https') !== false) {
            throw new \LogicException('Invalid Url : ' . var_export($url, true));
        }

        $this->webHookUrl = $url;
    }
}
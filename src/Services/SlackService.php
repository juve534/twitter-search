<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch\Services;

use \GuzzleHttp\Client;

/**
 * Class Slack
 */
class SlackService implements NotificationServiceInterface
{
    private string $webHookUrl;

    public function __construct(private Client $client)
    {
        $this->webHookUrl = getenv('WEB_HOOK_URL');
    }

    public function sendMessage(string|int $text): void
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
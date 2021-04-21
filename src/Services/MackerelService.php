<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch\Services;

use Mackerel\Client;

class MackerelService implements NotificationServiceInterface
{
    public function __construct(private Client $client)
    {}

    public function sendMessage(string|int $count): void
    {
        $hostId = getenv('MACKEREL_HOST_ID');
        $host   = $this->client->getHost($hostId);
        $metric = [
            'hostId' => $host->id,
            'time' => time(),
            'name' => getenv('CUSTOM_METRIC_TWITTER'),
            'value' => $count,
        ];
        $this->client->postMetrics([$metric]);
    }
}
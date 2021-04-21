<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch\Services;

interface NotificationServiceInterface
{
    public function sendMessage(string|int $text): void;
}
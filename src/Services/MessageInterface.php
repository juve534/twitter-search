<?php

declare(strict_types=1);

namespace Juve534\TwitterSearch\Services;


interface MessageInterface
{
    public function sendMessage(array $message): void;
    public function receiveMessage(): ?array;
}
<?php

declare(strict_types=1);

namespace RD\ErrorLog\Queue\Message;

final class ErrorSlackMessage
{
    public function __construct(
        public readonly array $message,
        public readonly string $token,
        public readonly string $channel
    ) {
    }
}

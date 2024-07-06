<?php

declare(strict_types=1);

namespace RD\ErrorLog\Queue\Handler;

use RD\ErrorLog\Queue\Message\ErrorSlackMessage;
use RD\ErrorLog\Service\SlackService;

class ErrorSlackMessageHandler
{
    public function __construct(
        private readonly SlackService $slackService
    ) {
    }

    public function __invoke(ErrorSlackMessage $message)
    {
        $this->slackService->sendBlocks($message->message, $message->token, $message->channel);
    }
}

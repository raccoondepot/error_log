<?php

declare(strict_types=1);

namespace RD\ErrorLog\Queue\Handler;

use RD\ErrorLog\Queue\Message\ReportSlackMessage;
use RD\ErrorLog\Service\SlackService;
use SlackPhp\BlockKit\Kit;

class ReportSlackMessageHandler
{
    public function __construct(
        private readonly SlackService $slackService
    ) {
    }

    public function __invoke(ReportSlackMessage $message)
    {
        $msg = Kit::message()
            ->blocks(
                Kit::section(':red_circle: *The errors report*'),
                Kit::divider(),
            );
        foreach ($message->reportEvent->getErrors() as $error) {
            $code = $error['code'] ? ' (Code: ' . $error['code'] . ') ' : '';
            $msg->blocks(
                Kit::section('*Error message:* ' . $error['message'] . $code . $error['file'] . 'Count: ' . $error['count'] . ' times'),
            );
        }

        $this->slackService->sendBlocks($msg->getBlocks(), $message->settings->getSlackAuthToken(), $message->settings->getSlackChannelId());
    }
}

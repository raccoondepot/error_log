<?php

declare(strict_types=1);

namespace RD\ErrorLog\Queue\Message;

use RD\ErrorLog\Domain\Event\ReportEvent;
use RD\ErrorLog\Domain\Model\Settings;

final class ReportSlackMessage
{
    public function __construct(
        public readonly Settings $settings,
        public readonly ReportEvent $reportEvent,
    ) {
    }
}

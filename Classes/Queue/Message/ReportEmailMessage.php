<?php

declare(strict_types=1);

namespace RD\ErrorLog\Queue\Message;

use RD\ErrorLog\Domain\Event\ReportEvent;
use RD\ErrorLog\Domain\Model\BackendUser;

final class ReportEmailMessage
{
    public function __construct(
        public readonly BackendUser $user,
        public readonly ReportEvent $reportEvent,
    ) {
    }
}

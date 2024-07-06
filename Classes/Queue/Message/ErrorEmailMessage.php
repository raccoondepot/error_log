<?php

declare(strict_types=1);

namespace RD\ErrorLog\Queue\Message;

use RD\ErrorLog\Domain\Model\BackendUser;
use RD\ErrorLog\Domain\Model\Error;

final class ErrorEmailMessage
{
    public function __construct(
        public readonly BackendUser $user,
        public readonly Error $errorEvent,
    ) {
    }
}

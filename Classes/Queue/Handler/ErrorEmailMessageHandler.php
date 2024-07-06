<?php

declare(strict_types=1);

namespace RD\ErrorLog\Queue\Handler;

use RD\ErrorLog\Queue\Message\ErrorEmailMessage;
use RD\ErrorLog\Service\MailService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ErrorEmailMessageHandler
{
    const TEMPLATE_PATH = 'EXT:error_log/Resources/Private/Templates/Email/ErrorOccurredEmail.html';

    public function __construct(
        private readonly MailService $mailService
    ) {
    }

    public function __invoke(ErrorEmailMessage $message)
    {
        $this->mailService->sendEmail(
            self::TEMPLATE_PATH,
            $message->user,
            $message->errorEvent,
            LocalizationUtility::translate('email.subject_error_occurred', 'error_log')
        );
    }
}

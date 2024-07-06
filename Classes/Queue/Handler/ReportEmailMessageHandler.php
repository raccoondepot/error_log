<?php

declare(strict_types=1);

namespace RD\ErrorLog\Queue\Handler;

use RD\ErrorLog\Queue\Message\ReportEmailMessage;
use RD\ErrorLog\Service\MailService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ReportEmailMessageHandler
{
    const TEMPLATE_PATH = 'EXT:error_log/Resources/Private/Templates/Email/ReportEmail.html';

    public function __construct(
        private readonly MailService $mailService
    ) {
    }

    public function __invoke(ReportEmailMessage $message)
    {
        $this->mailService->sendEmail(
            self::TEMPLATE_PATH,
            $message->user,
            $message->reportEvent->getErrors(),
            LocalizationUtility::translate('email.subject_report', 'error_log')
        );
    }
}

<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Enum\Option;
use RD\ErrorLog\Domain\Event\ErrorEvent;
use RD\ErrorLog\Domain\Repository\BackendUserRepository;
use RD\ErrorLog\Domain\Repository\ErrorRepository;
use RD\ErrorLog\Service\MailService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class EmailOnEventOccurredListener
{
    const TEMPLATE_PATH = 'EXT:error_log/Resources/Private/Templates/Email/ErrorOccurredEmail.html';

    private MailService $mailService;
    private BackendUserRepository $backendUserRepository;
    private ErrorRepository $errorRepository;

    public function __construct(MailService $mailService, BackendUserRepository $backendUserRepository, ErrorRepository $errorRepository)
    {
        $this->mailService = $mailService;
        $this->backendUserRepository = $backendUserRepository;
        $this->errorRepository = $errorRepository;
    }

    public function __invoke(ErrorEvent $event)
    {
        $users = $this->backendUserRepository->getUsersWithEnabledErrorsNotifications();
        $error = $event->getError();

        foreach ($users as $user) {
            if ($user->errorlogOccurrenceType === Option::EACH) {
                $this->notifyUser($user, $error);
            } elseif ($user->errorlogOccurrenceType === Option::FIRST && $event->isFirstOccurrence()) {
                $this->notifyUser($user, $error);
            }
        }
    }

    private function notifyUser($user, $error)
    {
        $this->mailService->sendEmail(
            self::TEMPLATE_PATH,
            $user,
            $error,
            LocalizationUtility::translate('email.subject_error_occurred', 'error_log')
        );
    }
}

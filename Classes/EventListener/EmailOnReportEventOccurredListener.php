<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Event\ReportEvent;
use RD\ErrorLog\Domain\Repository\BackendUserRepository;
use RD\ErrorLog\Service\MailService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class EmailOnReportEventOccurredListener
{
    const TEMPLATE_PATH = 'EXT:error_log/Resources/Private/Templates/Email/ReportEmail.html';
    private StandaloneView $standaloneView;
    private MailService $mailService;
    private BackendUserRepository $backendUserRepository;

    public function __construct(StandaloneView $standaloneView, MailService $mailService, BackendUserRepository $backendUserRepository)
    {
        $this->standaloneView = $standaloneView;
        $this->mailService = $mailService;
        $this->backendUserRepository = $backendUserRepository;
    }

    public function __invoke(ReportEvent $event)
    {
        $users = $this->backendUserRepository->getUsersWithEnabledReporting();
        foreach ($users as $user) {
            $this->mailService->sendEmail(
                self::TEMPLATE_PATH,
                $user,
                $event->getErrors(),
                LocalizationUtility::translate('email.subject_report', 'error_log')
            );
        }
    }
}

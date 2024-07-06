<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Event\ReportEvent;
use RD\ErrorLog\Domain\Repository\BackendUserRepository;
use RD\ErrorLog\Queue\Message\ReportEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailOnReportEventOccurredListener
{
    public function __construct(
        private readonly BackendUserRepository $backendUserRepository,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(ReportEvent $event)
    {
        $users = $this->backendUserRepository->getUsersWithEnabledReporting();
        foreach ($users as $user) {
            $this->messageBus->dispatch(new ReportEmailMessage($user, $event));
        }
    }
}

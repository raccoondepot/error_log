<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Enum\Option;
use RD\ErrorLog\Domain\Event\ErrorEvent;
use RD\ErrorLog\Domain\Repository\BackendUserRepository;
use RD\ErrorLog\Queue\Message\ErrorEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailOnEventOccurredListener
{
    public function __construct(
        private readonly BackendUserRepository $backendUserRepository,
        private readonly MessageBusInterface $messageBus
    ) {
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
        $this->messageBus->dispatch(new ErrorEmailMessage($user, $error));
    }
}

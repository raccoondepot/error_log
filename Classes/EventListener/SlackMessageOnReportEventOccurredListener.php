<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Event\ReportEvent;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use RD\ErrorLog\Queue\Message\ReportSlackMessage;
use RD\ErrorLog\Traits\SlackSettingsTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class SlackMessageOnReportEventOccurredListener
{
    use SlackSettingsTrait;

    public function __construct(
        private readonly SettingsRepository $settingsRepository,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(ReportEvent $event)
    {
        $settings = $this->settingsRepository->getSettings();
        if ($this->isSlackEnabledAndSettingsAreSet($settings) === false) {
            return;
        }

        $this->messageBus->dispatch(new ReportSlackMessage($settings, $event));
    }
}

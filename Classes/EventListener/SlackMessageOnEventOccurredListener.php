<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Enum\Option;
use RD\ErrorLog\Domain\Event\ErrorEvent;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use RD\ErrorLog\Queue\Message\ErrorSlackMessage;
use RD\ErrorLog\Traits\SlackSettingsTrait;
use SlackPhp\BlockKit\Kit;
use Symfony\Component\Messenger\MessageBusInterface;

class SlackMessageOnEventOccurredListener
{
    use SlackSettingsTrait;

    public function __construct(
        private readonly SettingsRepository $settingsRepository,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(ErrorEvent $event)
    {
        $settings = $this->settingsRepository->getSettings();

        if ($this->isSlackEnabledAndSettingsAreSet($settings) === false) {
            return;
        }

        if ($event->isFirstOccurrence() === false && $settings->getSlackOccurrenceType() === Option::FIRST) {
            return;
        }

        $msg = Kit::message()
            ->blocks(
                Kit::section(':red_circle: *The error occurred!*'),
                Kit::divider(),
                Kit::section('*Error message:* ' . $event->getError()->getMessage() . ' (' . $event->getError()->getCode() . ')'),
                Kit::section(' <' . $event->getUrl() . '| Open error in TYPO3 backend>')
            );

        $this->messageBus->dispatch(new ErrorSlackMessage($msg->getBlocks(), $settings->getSlackAuthToken(), $settings->getSlackChannelId()));
    }
}

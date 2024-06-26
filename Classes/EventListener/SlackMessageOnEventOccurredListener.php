<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Enum\Option;
use RD\ErrorLog\Domain\Event\ErrorEvent;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use RD\ErrorLog\Service\SlackService;
use RD\ErrorLog\Traits\SlackSettingsTrait;
use SlackPhp\BlockKit\Surfaces\Message;

class SlackMessageOnEventOccurredListener
{
    use SlackSettingsTrait;

    private SlackService $slackService;
    private SettingsRepository $settingsRepository;

    public function __construct(SlackService $service, SettingsRepository $settingsRepository)
    {
        $this->slackService = $service;
        $this->settingsRepository = $settingsRepository;
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

        $msg = Message::new();
        $msg->text(':red_circle: *The error occurred!*' . ' <' . $event->getUrl() . '|Open error in TYPO3 backend>')
            ->divider()
            ->text('_Message error:_ ' . $event->getError()->getMessage());
        if ($event->getError()->getCode() !== 0) {
            $msg->text(' _Code:_ ' . $event->getError()->getCode());
        }
        $this->slackService->sendBlocks(json_encode($msg->getBlocks()), $settings->getSlackAuthToken(), $settings->getSlackChannelId());
    }
}

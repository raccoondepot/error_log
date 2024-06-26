<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Enum\Option;
use RD\ErrorLog\Domain\Event\ErrorEvent;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use RD\ErrorLog\Service\SlackService;
use RD\ErrorLog\Traits\SlackSettingsTrait;
use SlackPhp\BlockKit\Blocks\BlockImage;
use SlackPhp\BlockKit\Blocks\Divider;
use SlackPhp\BlockKit\Blocks\Section;
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

        $msg = new Message(
            blocks: [
                new Section(':red_circle: *The error occurred!*' . ' <' . $event->getUrl() . '|Open error in TYPO3 backend>')
            ],
            ephemeral: true
        );

        $this->slackService->sendBlocks($msg->toJson(true), $settings->getSlackAuthToken(), $settings->getSlackChannelId());
    }
}

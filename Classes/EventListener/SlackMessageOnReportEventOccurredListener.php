<?php

declare(strict_types=1);

namespace RD\ErrorLog\EventListener;

use RD\ErrorLog\Domain\Event\ReportEvent;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use RD\ErrorLog\Service\SlackService;
use RD\ErrorLog\Traits\SlackSettingsTrait;

class SlackMessageOnReportEventOccurredListener
{
    use SlackSettingsTrait;

    private SlackService $slackService;
    private SettingsRepository $settingsRepository;

    public function __construct(SlackService $service, SettingsRepository $settingsRepository)
    {
        $this->slackService = $service;
        $this->settingsRepository = $settingsRepository;
    }

    public function __invoke(ReportEvent $event)
    {
        $settings = $this->settingsRepository->getSettings();
        if ($this->isSlackEnabledAndSettingsAreSet($settings) === false) {
            return;
        }

        $errors = $event->getErrors();
        $message = 'Errors occurred: ' . count($errors) . ' errors';
        foreach ($errors as $error) {
            $message .= PHP_EOL . 'Error occurred: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line'] . ' with code ' . $error['code'];
        }

        $this->slackService->sendMessage($message, $settings->getSlackAuthToken(), $settings->getSlackChannelId());
    }
}

<?php

declare(strict_types=1);

namespace RD\ErrorLog\Traits;

trait SlackSettingsTrait
{
    private function isSlackEnabledAndSettingsAreSet($settings): bool
    {
        return $settings->getSlackEnable() === true && $settings->getSlackAuthToken() !== '' && $settings->getSlackChannelId() !== '';
    }
}

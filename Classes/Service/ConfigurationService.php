<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use TYPO3\CMS\Core\Configuration\ConfigurationManager;

class ConfigurationService
{
    private const HANDLERS = [
        'SYS/errorHandler' => 'error_log\\Classes\\Handler\\ErrorHandler',
        'SYS/debugExceptionHandler' => 'error_log\\Classes\\Handler\\DebugExceptionHandler',
        'SYS/productionExceptionHandler' => 'error_log\\Classes\\Handler\\ProductionExceptionHandler',
    ];

    private ConfigurationManager $configurationManager;

    public function __construct(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function checkAreHandlersIsSet(): bool
    {
        $isset = true;
        foreach (self::HANDLERS as $key => $value) {
            try {
                $this->configurationManager->getLocalConfigurationValueByPath($key);
            } catch (\Exception $e) {
                $isset = false;
                break;
            }
        }

        return $isset;
    }

    public function modifyHandlers(bool $set): void
    {
        if ($set) {
            foreach (self::HANDLERS as $key => $value) {
                $this->configurationManager->setLocalConfigurationValueByPath($key, $value);
            }
        } else {
            $this->configurationManager->removeLocalConfigurationKeysByPath(array_keys(self::HANDLERS));
        }
    }
}

<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use TYPO3\CMS\Core\Configuration\ConfigurationManager;

class ConfigurationService
{
    private const HANDLERS = [
        'SYS/errorHandler' => 'RD\\ErrorLog\\Handler\\ErrorHandler',
        'SYS/debugExceptionHandler' => 'RD\\ErrorLog\\Handler\\DebugExceptionHandler',
        'SYS/productionExceptionHandler' => 'RD\\ErrorLog\\Handler\\ProductionExceptionHandler',
    ];

    public function __construct(
        private readonly ConfigurationManager $configurationManager
    ) {
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

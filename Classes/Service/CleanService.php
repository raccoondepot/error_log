<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use RD\ErrorLog\Domain\Repository\ErrorRepository;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

class CleanService
{
    private EventDispatcher $eventDispatcher;
    private SettingsRepository $settingsRepository;
    private ErrorRepository $errorRepository;

    public function __construct(
        EventDispatcher $eventDispatcher,
        SettingsRepository $settingsRepository,
        ErrorRepository $errorRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->settingsRepository = $settingsRepository;
        $this->errorRepository = $errorRepository;
    }

    public function run(): void
    {
        if (date('G') == 0) {
            $this->cleanErrors();
        }
    }

    private function cleanErrors(): void
    {
        $expireDays = $this->settingsRepository->getSettings()->getGeneralExpireDays();
        if ($expireDays > 0) {
            $this->errorRepository->deleteErrorsOlderThan($expireDays);
        }
    }
}

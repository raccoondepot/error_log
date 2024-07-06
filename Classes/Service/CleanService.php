<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use RD\ErrorLog\Domain\Repository\ErrorRepository;
use RD\ErrorLog\Domain\Repository\SettingsRepository;

class CleanService
{
    public function __construct(
        private readonly SettingsRepository $settingsRepository,
        private readonly ErrorRepository $errorRepository
    ) {
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

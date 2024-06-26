<?php

declare(strict_types=1);

namespace RD\ErrorLog\Task;

use RD\ErrorLog\Service\CleanService;
use RD\ErrorLog\Service\ReportService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class ServiceManagerTask extends AbstractTask
{
    public function execute(): bool
    {
        GeneralUtility::makeInstance(ReportService::class)->run();
        GeneralUtility::makeInstance(CleanService::class)->run();
        return true;
    }
}

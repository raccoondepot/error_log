<?php

declare(strict_types=1);

namespace RD\ErrorLog\Updates;

use RD\ErrorLog\Task\ServiceManagerTask;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Scheduler;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class InstallExtension implements UpgradeWizardInterface
{
    public function getIdentifier(): string
    {
        return 'errorLogInstallTask';
    }

    public function getTitle(): string
    {
        return 'Add error log scheduler task, add error handlers';
    }

    public function getDescription(): string
    {
        return 'Add error log scheduler task, add error handlers';
    }

    public function executeUpdate(): bool
    {
        $scheduler = GeneralUtility::makeInstance(Scheduler::class);
        $task = GeneralUtility::makeInstance(ServiceManagerTask::class);
        $task->registerRecurringExecution(time(), 86400);
        $task->setDescription('This is error log service manager task');
        $scheduler->addTask($task);
        $configurationService = GeneralUtility::makeInstance(\RD\ErrorLog\Service\ConfigurationService::class);
        $configurationService->modifyHandlers(true);
        return true;
    }

    public function updateNecessary(): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return 50;
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}

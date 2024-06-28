<?php

declare(strict_types=1);

namespace RD\ErrorLog\Updates;

use RD\ErrorLog\Service\ConfigurationService;
use RD\ErrorLog\Task\ServiceManagerTask;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Scheduler\Domain\Repository\SchedulerTaskRepository;

#[UpgradeWizard('errorLogInstallTask')]
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
        $schedulerTaskRepository = GeneralUtility::makeInstance(SchedulerTaskRepository::class);
        $task = GeneralUtility::makeInstance(ServiceManagerTask::class);
        $task->registerRecurringExecution(time(), 86400);
        $task->setDescription('This is error log service manager task');
        $schedulerTaskRepository->add($task);
        $configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
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

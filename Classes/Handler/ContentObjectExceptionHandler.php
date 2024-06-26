<?php

declare(strict_types=1);

namespace RD\ErrorLog\Handler;

use Psr\Log\LoggerInterface;
use RD\ErrorLog\Domain\Repository\SettingsRepository;
use RD\ErrorLog\Service\LogWriter;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\Exception\ProductionExceptionHandler as ProductionExceptionHandlerCore;

class ContentObjectExceptionHandler extends ProductionExceptionHandlerCore
{
    private SettingsRepository $settingsRepository;
    public function __construct(Context $context, Random $random, LoggerInterface $logger, SettingsRepository $settingsRepository)
    {
        parent::__construct($context, $random, $logger);
        $this->settingsRepository = $settingsRepository;
    }

    public function handle(\Exception $exception, AbstractContentObject $contentObject = null, $contentObjectConfiguration = []): string
    {
        $this->writeMessage($exception);
        return parent::handle($exception, $contentObject, $contentObjectConfiguration);
    }

    private function writeMessage(\Throwable $exception)
    {
        if ($this->settingsRepository->getSettings()->getGeneralEnable() === false) {
            return;
        }
        $logService = GeneralUtility::makeInstance(LogWriter::class);
        $logService->writeError($exception);
    }
}

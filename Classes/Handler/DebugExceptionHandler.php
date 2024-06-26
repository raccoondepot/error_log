<?php

declare(strict_types=1);

namespace RD\ErrorLog\Handler;

use RD\ErrorLog\Service\LogWriter;
use TYPO3\CMS\Core\Error\DebugExceptionHandler as DebugExceptionHandlerCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DebugExceptionHandler extends DebugExceptionHandlerCore
{
    public function echoExceptionWeb(\Throwable $exception)
    {
        $this->writeMessage($exception, LogWriter::CONTEXT_WEB);
        parent::echoExceptionWeb($exception);
    }

    public function echoExceptionCLI(\Throwable $exception)
    {
        $this->writeMessage($exception, LogWriter::CONTEXT_CLI);
        parent::echoExceptionCLI($exception);
    }

    private function writeMessage(\Throwable $exception, string $channel)
    {
        $logService = GeneralUtility::makeInstance(LogWriter::class);
        $logService->writeError($exception, $channel);
    }
}

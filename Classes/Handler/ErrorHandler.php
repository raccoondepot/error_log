<?php

declare(strict_types=1);

namespace RD\ErrorLog\Handler;

use RD\ErrorLog\Service\LogWriter;
use TYPO3\CMS\Core\Exception;

class ErrorHandler extends \TYPO3\CMS\Core\Error\ErrorHandler
{
    public function registerErrorHandler()
    {
        set_error_handler([$this, 'handleError']);
    }

    public function handleError($errorLevel, $errorMessage, $errorFile, $errorLine)
    {
        $this->writeMessage('Error: ' . $errorMessage . ' in ' . $errorFile . ' line ' . $errorLine);
        return parent::handleError($errorLevel, $errorMessage, $errorFile, $errorLine);
    }

    private function writeMessage($exception)
    {
        $logService = new LogWriter();
        if ($exception instanceof Exception) {
            $logService->writeError($exception);
        } else {
            $logService->writeError(new Exception($exception));
        }
    }
}

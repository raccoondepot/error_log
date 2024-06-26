<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Event;

use Exception;
use RD\ErrorLog\Domain\Model\Error;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ErrorEvent
{
    private Error $error;
    private bool $isFirstOccurrence;

    public function __construct(Error $error, bool $isFirstOccurrence)
    {
        $this->error = $error;
        $this->isFirstOccurrence = $isFirstOccurrence;
    }
    public function getError(): Error
    {
        return $this->error;
    }

    public function getUrl(): string
    {
        try {
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $route = 'system_ErrorLogTxErrorlog';
            $parameters = [
                'tx_errorlog_system_errorlogtxerrorlog[uid]' => $this->error->getUid(),
                'tx_errorlog_system_errorlogtxerrorlog[action]' => 'view',
            ];
            $referenceType = 'share';

            return (string)$uriBuilder->buildUriFromRoute($route, $parameters, $referenceType);
        } catch (Exception $e) {
            return '';
        }
    }

    public function isFirstOccurrence(): bool
    {
        return $this->isFirstOccurrence;
    }
}

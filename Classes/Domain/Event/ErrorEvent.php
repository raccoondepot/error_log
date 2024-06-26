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
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $route = 'error_log';
            $parameters = [
                'uid' => $this->error->getUid(),
                'action' => 'view',
            ];

            $url = (string) $uriBuilder->buildUriFromRoute($route, $parameters, UriBuilder::ABSOLUTE_URL);
            return $url;
    }

    public function isFirstOccurrence(): bool
    {
        return $this->isFirstOccurrence;
    }
}

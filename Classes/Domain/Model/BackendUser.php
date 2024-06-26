<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Model;

class BackendUser extends \TYPO3\CMS\Beuser\Domain\Model\BackendUser
{
    public int $errorlogEnableEmail = 0;
    public int $errorlogReportType = 0;
    public int $errorlogOccurrenceType = 0;

    public function setErrorlogEnableEmail(int $errorlogEnableEmail): void
    {
        $this->errorlogEnableEmail = $errorlogEnableEmail;
    }

    public function getErrorlogEnableEmail(): int
    {
        return $this->errorlogEnableEmail;
    }

    public function setErrorlogReportType(int $errorlogReportType): void
    {
        $this->errorlogReportType = $errorlogReportType;
    }

    public function getErrorlogReportType(): int
    {
        return $this->errorlogReportType;
    }

    public function setErrorlogOccurrenceType(int $errorlogOccurrenceType): void
    {
        $this->errorlogOccurrenceType = $errorlogOccurrenceType;
    }
    public function getErrorlogOccurrenceType(): int
    {
        return $this->errorlogOccurrenceType;
    }
}

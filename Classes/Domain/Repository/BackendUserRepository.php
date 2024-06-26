<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Repository;

use RD\ErrorLog\Domain\Enum\Option;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository as Typo3BackendUserRepository;

class BackendUserRepository extends Typo3BackendUserRepository
{
    public function getUsersWithEnabledErrorsNotifications()
    {
        $query = $this->createQuery();
        $query->matching($query->equals('errorlog_enable_email', 1));
        $query->matching($query->greaterThan('errorlog_occurrence_type', Option::NONE));
        return $query->execute();
    }

    public function getUsersWithEnabledReporting(int $frequency = 0)
    {
        $query = $this->createQuery();
        $query->matching($query->equals('errorlog_enable_email', 1));
        $query->matching($query->greaterThan('errorlog_occurrence_type', $frequency));
        return $query->execute();
    }
}

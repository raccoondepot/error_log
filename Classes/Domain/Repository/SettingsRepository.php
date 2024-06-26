<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use RD\ErrorLog\Domain\Model\Settings;

class SettingsRepository extends Repository
{
    public function getSettings(): Settings
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->setLimit(1);
        $result = $query->execute();

        if ($result->count() > 0) {
            return $result->getFirst();
        } else {
            $settings = new Settings();
            $this->add($settings);
            $this->persistenceManager->add($settings);
            $this->persistenceManager->persistAll();
            return $settings;
        }
    }
}

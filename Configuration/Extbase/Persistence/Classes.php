<?php

declare(strict_types=1);

return [
    \TYPO3\CMS\Beuser\Domain\Model\BackendUser::class => [
        'subclasses' => [
            '\RD\ErrorLog\Domain\Model\BackendUser' => \RD\ErrorLog\Domain\Model\BackendUser::class,
        ],
    ],
    \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository::class => [
        'subclasses' => [
            '\RD\ErrorLog\Domain\Repository\BackendUserRepository' => \RD\ErrorLog\Domain\Repository\BackendUserRepository::class,
        ],
    ],
    \RD\ErrorLog\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users'
    ],
];

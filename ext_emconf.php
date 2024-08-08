<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Advanced Error Log',
    'description' => 'This TYPO3 extension manages errors and exceptions, even before TYPO3 fully loads. It groups and displays errors in the backend, with detailed information and stack traces. Configurable notifications and reports via email and Slack keep you informed, while AI assistance aids in resolving issues.',
    'category' => 'extension',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'symfony/http-client' => '',
            'slack-php/slack-block-kit' => '',
            'erusev/parsedown' => ''
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'RD\\ErrorLog\\' => 'Classes',
        ],
    ],
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Serhii Voronov, Rostyslav Matviiv, Andrii Pozdieiev',
    'author_email' => 'info@raccoon-depot.com',
    'author_company' => 'Raccoon Depot',
    'version' => '12.4.4',
];

<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Advanced Error Log',
    'description' => 'Error log extension for TYPO3 CMS',
    'category' => 'extension',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'symfony/http-client' => ''
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
    'version' => '11.5.2',
];

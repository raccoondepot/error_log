<?php

use RD\ErrorLog\Backend\Controller\LogErrorModuleController;

return [
    'error_log' => [
        'parent' => 'system',
        'position' => ['top'],
        'access' => 'admin',
        'path' => 'module/system/ErrorLog/',
        'labels' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'Error Log',
        'iconIdentifier' => 'module-error-log',
        'controllerActions' => [
            LogErrorModuleController::class => [
                'index', 'settings', 'saveSettings', 'delete', 'view',
            ],
        ],
    ],
];
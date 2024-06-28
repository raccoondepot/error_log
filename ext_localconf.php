<?php

defined('TYPO3') or die('Access denied.');

use RD\ErrorLog\Task\ServiceManagerTask;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function ($extensionKey) {
    ExtensionManagementUtility::addTypoScript(
        $extensionKey,
        'setup',
        "@import 'EXT:error_log/Configuration/TypoScript/setup.typoscript'"
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][ServiceManagerTask::class] = [
        'extension' => $extensionKey,
        'title' => 'Error log service manager task',
        'description' => 'This is error log which runs service manager task for clean up and reporting',
        'additionalFields' => ''
    ];
})('error_log');

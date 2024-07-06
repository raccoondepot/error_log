<?php

call_user_func(
    function () {
        $extensionKey = 'error_log';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extensionKey,
            'Configuration/TypoScript',
            'Error Log'
        );
    }
);

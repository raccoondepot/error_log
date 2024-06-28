<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die('Access denied.');

defined('TYPO3') or die();

$GLOBALS['TYPO3_USER_SETTINGS']['columns'] = array_merge($GLOBALS['TYPO3_USER_SETTINGS']['columns'], [
    'errorlog_enable_email' => [
        'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_enable_email',
        'type' => 'check',
        'csh' => 'errorlog_enable_email',
        'table' => 'be_users'
    ],
    'errorlog_report_type' => [
        'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_report_type',
        'type' => 'select',
        'csh' => 'errorlog_report_type',
        'items' => [
            '0' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_0',
            '1' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_1',
            '2' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_2',
            '3' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_3',
            '4' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_4',
        ],
        'table' => 'be_users'
    ],
    'errorlog_occurrence_type' => [
        'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_occurrence_type',
        'type' => 'select',
        'csh' => 'errorlog_occurrence_type',
        'items' => [
            '0' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_0',
            '1' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_1',
            '2' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_2',
        ],
        'table' => 'be_users'
    ],
]);

ExtensionManagementUtility::addFieldsToUserSettings(
    '--div--;LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_tab,errorlog_enable_email,errorlog_report_type,errorlog_occurrence_type',
    'after:resetConfiguration',
);

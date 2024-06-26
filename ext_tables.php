<?php

declare(strict_types=1);

defined('TYPO3') or die('Access denied.');

use RD\ErrorLog\Backend\Controller\LogErrorModuleController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::registerModule(
    'ErrorLog',
    'system',
    'tx_ErrorLog',
    'top',
    [
        LogErrorModuleController::class => 'index, settings, saveSettings, delete, view',
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:error_log/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_mod.xlf',
    ]
);

$GLOBALS['TYPO3_USER_SETTINGS']['columns'] = array_merge($GLOBALS['TYPO3_USER_SETTINGS']['columns'], [
    /* 'errorlog_slack' => [
        'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_slack',
        'type' => 'text',
        'csh' => 'errorlog_slack',
        'table' => 'be_users'
    ], */
    'errorlog_enable_email' => [
        'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_enable_email',
        'type' => 'check',
        'csh' => 'errorlog_enable_email',
        'table' => 'be_users'
    ],
    /* 'errorlog_enable_slack' => [
        'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_enable_slack',
        'type' => 'check',
        'csh' => 'errorlog_enable_slack',
        'table' => 'be_users'
    ], */
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

/* \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings(
    'errorlog_slack',
    'after:email',
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings(
    '--div--;LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_tab,errorlog_enable_email,errorlog_enable_slack,errorlog_report_type,errorlog_occurance_type',
    'after:resetConfiguration',
); */

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToUserSettings(
    '--div--;LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_tab,errorlog_enable_email,errorlog_report_type,errorlog_occurrence_type',
    'after:resetConfiguration',
);

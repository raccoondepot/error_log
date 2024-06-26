<?php

defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'be_users',
    [
        /* 'errorlog_slack' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_slack',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ]
        ], */
        'errorlog_enable_email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_enable_email',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0
            ]
        ],
        /* 'errorlog_enable_slack' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_enable_slack',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0
            ]
        ], */
        'errorlog_report_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_report_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_0', 0],
                    ['LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_1', 1],
                    ['LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_2', 2],
                    ['LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_3', 3],
                    ['LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_4', 4]
                ]
            ]
        ],
        'errorlog_occurrence_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_occurrence_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_0', 0],
                    ['LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_1', 1],
                    ['LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_2', 2]
                ]
            ]
        ],
    ]
);

/* \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'be_users',
    'errorlog_slack',
    '',
    'after:email'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'be_users',
    '--div--;LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_tab, errorlog_enable_email, errorlog_enable_slack, errorlog_report_type, errorlog_occurance_type',
    '',
    'after:notes'
); */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'be_users',
    '--div--;LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_tab, errorlog_enable_email, errorlog_report_type, errorlog_occurrence_type',
    '',
    'after:notes'
);

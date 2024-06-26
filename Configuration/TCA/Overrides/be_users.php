<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'be_users',
    [
        'errorlog_enable_email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_enable_email',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0
            ]
        ],
        'errorlog_report_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_report_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_0', 'value' => 0],
                    ['label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_1', 'value' => 1],
                    ['label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_2', 'value' => 2],
                    ['label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_3', 'value' => 3],
                    ['label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_report_4', 'value' => 4]
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
                    ['label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_0', 'value' => 0],
                    ['label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_1', 'value' => 1],
                    ['label' => 'LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_option_occurence_2', 'value' => 2]
                ]
            ]
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'be_users',
    '--div--;LLL:EXT:error_log/Resources/Private/Language/locallang_db.xlf:be_users.errorlog_tab, errorlog_enable_email, errorlog_report_type, errorlog_occurrence_type',
    '',
    'after:notes'
);

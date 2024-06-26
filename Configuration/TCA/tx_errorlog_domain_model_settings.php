<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:error_log/Resources/Private/Language/locallang.xlf:errors_log',
        'label' => 'pid',
        'tstamp' => 'tstamp',
        'adminOnly' => true,
        'rootLevel' => 1,
        'hideTable' => true,
    ],
    'columns' => [
        'general_enable' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'general_expire_days' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'slack_enable' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'slack_auth_token' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'slack_channel_id' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'slack_report_type' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'slack_occurrence_type' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'openai_enable' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'openai_auth_token' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'openai_model' => [
            'config' => [
                'type' => 'input',
            ],
        ],
        'pre_prompt' => [
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
];

<?php

return [
    'error_log_ai_ask' => [
        'path' => '/error-log/ai/ask',
        'target' => \RD\ErrorLog\Backend\Controller\AIController::class . '::askAction'
    ],
    'error_log_ai_test' => [
        'path' => '/error-log/ai/test',
        'target' => \RD\ErrorLog\Backend\Controller\AIController::class . '::testAction'
    ],
    'error_log_slack_test' => [
        'path' => '/error-log/slack/test',
        'target' => \RD\ErrorLog\Backend\Controller\SlackController::class . '::testAction'
    ],
];

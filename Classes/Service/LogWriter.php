<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use mysqli;
use Psr\Container\ContainerInterface;
use RD\ErrorLog\Domain\Model\Error;
use RD\ErrorLog\Domain\Event\ErrorEvent;
use RD\ErrorLog\Domain\Repository\ErrorRepository;
use TYPO3\CMS\Backend\FrontendBackendUserAuthentication;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LogWriter implements SingletonInterface
{
    public const CONTEXT_WEB = 'WEB';
    public const CONTEXT_CLI = 'CLI';

    private function collectInformationForEvent(\Throwable $exception, string $channel): array
    {
        [$file, $line, $traceDetails] = $this->clearTraceAndGetExceptionFileAndLineFromTrace($exception);

        if (str_contains($exception->getMessage(), 'Deprecated')) {
            $processedTraceDetails = [];
        } else {
            $processedTraceDetails = [];

            foreach ($traceDetails as $trace) {
                if (isset($trace['args'])) {
                    foreach ($trace['args'] as &$arg) {
                        if (is_object($arg)) {
                            $arg = get_class($arg);
                        } elseif (is_array($arg)) {
                            $arg = json_encode($arg);
                        }
                    }
                }
                $processedTraceDetails[] = $trace;
            }
        }
        $userId = 0;
        $workspace = 0;
        $data = [];
        $backendUser = $this->getBackendUser();

        if ($backendUser instanceof FrontendBackendUserAuthentication) {
            if (isset($backendUser->user['uid'])) {
                $userId = $backendUser->user['uid'];
            }
            if (isset($backendUser->workspace)) {
                $workspace = $backendUser->workspace;
            }
            if ($backUserId = $backendUser->getOriginalUserIdWhenInSwitchUserMode()) {
                $data['originalUser'] = $backUserId;
            }
        }
        $errorValues = [
            'data' => empty($data) ? '' : serialize($data),
            'page_uid' => isset($GLOBALS['TSFE']) ? (int) $GLOBALS['TSFE']->id : 0,
            'message' => $exception->getMessage() ?? '',
            'code' => $exception->getCode() ?? 0,
            'file' => $file ?? '',
            'line' => $line ?? 0,
            'trace' => json_encode($processedTraceDetails),
            'browser_info' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'server_name' => $_SERVER['SERVER_NAME'] ?? '',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'root_page_uid' => isset($GLOBALS['TSFE']) ? $GLOBALS['TSFE']->rootLine[0]['uid'] : 0,
            'crdate' => $GLOBALS['EXEC_TIME'] ?? time(),
            'IP' => (string) GeneralUtility::getIndpEnv('REMOTE_ADDR') ?? '',
            'user' =>  isset($GLOBALS['BE_USER']) ? $GLOBALS['BE_USER']->user['username'] : '',
            'user_id' => $userId ?? 0,
            'workspace' => $workspace ?? 0,
            'event_dispatched' => true,
            'channel' => $channel,
        ];

        return $errorValues;
    }

    private function clearTraceAndGetExceptionFileAndLineFromTrace(\Throwable $exception): array
    {
        $trace = $exception->getTrace();
        foreach ($trace as $t) {
            if (str_contains($t['class'] ?? '', 'LogWriter') || str_contains($t['class'] ?? '', 'ErrorHandler')) {
                array_shift($trace);
            }
        }

        return [$trace[0]['file'] ?? '', $trace[0]['line'], $trace];
    }

    public function writeError(\Throwable $exception, string $channel = self::CONTEXT_WEB): void
    {
        $errorValues = $this->collectInformationForEvent($exception, $channel);
        $container = $this->getContainer();
        if ($container === null) {
            $this->writeDirectlyIntoDatabase($errorValues);
        } else {
            $this->write($errorValues);
        }
    }

    private function write(array $errorValues): void
    {
        $errorRepository = GeneralUtility::makeInstance(ErrorRepository::class);
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_errorlog_domain_model_error');

        $connection->insert('tx_errorlog_domain_model_error', $errorValues);
        $errorValues['uid'] = (int) $connection->lastInsertId();
        $error = new Error($errorValues);
        $errorTypeHash = $errorRepository->generateErrorTypeHash($error);
        $isFirstOccurrence = $errorRepository->isFirstOccurrence($errorTypeHash);
        if ($isFirstOccurrence) {
            $errorRepository->createOccurrence($errorTypeHash, $error->getUid());
        }

        $eventDispatcher->dispatch(new ErrorEvent($error, $isFirstOccurrence));
    }

    private function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }

    private function getContainer(): ?ContainerInterface
    {
        $container = null;
        try {
            $container = GeneralUtility::getContainer();
        } catch (\Exception $e) {
            // Do nothing
        }
        return $container;
    }

    // We need to write directly into the database while the DI container is not available
    private function writeDirectlyIntoDatabase(array $errorValues): void
    {
        if (empty($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']) || !class_exists(mysqli::class)) {
            return;
        }

        $errorValues['event_dispatched'] = false;

        $servername = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'];
        $username = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'];
        $password = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'];
        $dbname = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'];
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            return;
        }

        foreach ($errorValues as $key => $value) {
            if (is_string($value)) {
                $errorValues[$key] = $conn->real_escape_string($value);
            }
            if (is_bool($value)) {
                $errorValues[$key] = (int) $value;
            }
        }

        $sql = "INSERT INTO tx_errorlog_domain_model_error (message, code, file, line, trace, browser_info, server_name, request_uri, root_page_uid, crdate, IP, user, user_id, workspace, event_dispatched)
                VALUES ('{$errorValues['message']}',
                        '{$errorValues['code']}',
                        '{$errorValues['file']}',
                        '{$errorValues['line']}',
                        '{$errorValues['trace']}',
                        '{$errorValues['browser_info']}',
                        '{$errorValues['server_name']}',
                        '{$errorValues['request_uri']}',
                        '{$errorValues['root_page_uid']}',
                        '{$errorValues['crdate']}',
                        '{$errorValues['IP']}',
                        '{$errorValues['user']}',
                        '{$errorValues['user_id']}',
                        '{$errorValues['workspace']}',
                        '{$errorValues['event_dispatched']}'
                )";

        $conn->query($sql);
        $conn->close();
    }
}

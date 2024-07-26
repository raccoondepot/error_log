<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

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
            'page_uid' => isset($GLOBALS['TSFE']) ? (int) $GLOBALS['TSFE']->id ?? 0 : 0,
            'message' => $exception->getMessage() ?? '',
            'code' => $exception->getCode() ?? 0,
            'file' => $file ?? '',
            'line' => $line ?? 0,
            'trace' => json_encode($processedTraceDetails),
            'browser_info' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'server_name' => $_SERVER['SERVER_NAME'] ?? '',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'root_page_uid' => isset($GLOBALS['TSFE']) ? $GLOBALS['TSFE']->rootLine[0]['uid'] ?? 0 : 0,
            'crdate' => $GLOBALS['EXEC_TIME'] ?? time(),
            'IP' => (string) GeneralUtility::getIndpEnv('REMOTE_ADDR') ?? '',
            'user' =>  $backendUser ? $backendUser->user['username'] ?? '' : '',
            'user_id' => $userId ?? 0,
            'workspace' => $workspace ?? 0,
            'event_dispatched' => 0,
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
        $errorValues['uid'] = $this->write($errorValues);
        $this->dispatchErrorEvent($errorValues);
    }

    private function dispatchErrorEvent(array $errorValues): void
    {
        try {
            $container = GeneralUtility::getContainer();
        } catch (Exception $e) {
            return;
        }

        if ($container->has(ErrorRepository::class) === false || $container->has(EventDispatcher::class) === false) {
            return;
        }

        $errorRepository = GeneralUtility::makeInstance(ErrorRepository::class);
        $eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $error = new Error($errorValues);
        $errorTypeHash = $errorRepository->generateErrorTypeHash($error);
        $isFirstOccurrence = $errorRepository->isFirstOccurrence($errorTypeHash);
        if ($isFirstOccurrence) {
            $errorRepository->createOccurrence($errorTypeHash, $error->getUid());
        }
        $eventDispatcher->dispatch(new ErrorEvent($error, $isFirstOccurrence));
        $errorRepository->setDispatchedEventForErrors([$error->getUid()]);
    }
    private function write(array $errorValues): int
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_errorlog_domain_model_error');
        $connection->insert('tx_errorlog_domain_model_error', $errorValues);
        return (int) $connection->lastInsertId();
    }

    private function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'] ?? null;
    }
}

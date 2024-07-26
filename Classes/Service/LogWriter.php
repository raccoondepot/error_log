<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service;

use RD\ErrorLog\Domain\Model\Error;
use RD\ErrorLog\Domain\Event\ErrorEvent;
use RD\ErrorLog\Domain\Repository\ErrorRepository;
use RD\ErrorLog\Service\Database\ConnectionService;
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

        // if TYPO3 is in completely booted state we can use TYPO3 API as normal
        if (GeneralUtility::getContainer()->get('boot.state')->complete) {
            $errorValues['uid'] = $this->write($errorValues);
            $this->dispatchErrorEvent($errorValues);
        } else {
            // if error happen too early the only thing we can do is to report it directly into the database "as raw as possible"
            try {
                $this->writeAsRawAsPossible($errorValues);
            } catch (\Throwable $e) {
                // no need to catch it, just silently exit
            }
        }
    }

    /**
     * Avoid ConnectionPool usage prior boot completion, as that is deprecated since #94979
     * https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/11.4/Deprecation-94979-UsingCacheManagerOrDatabaseConnectionsDuringTYPO3Bootstrap.html
     *
     * @param array $errorValues
     */
    private function writeAsRawAsPossible(array $errorValues): void
    {
        $connection = (new ConnectionService())->getConnectionForTable('tx_errorlog_domain_model_error');
        $errorValues = $this->prepareErrorValuesForRawQuery($errorValues);
        $fields = implode(', ', array_keys($errorValues));
        $values = implode(', ', array_values($errorValues));
        $connection->executeStatement(
            "INSERT INTO tx_errorlog_domain_model_error (" . $fields . ") VALUES (" . $values . ")"
        );
        $connection->close();
    }

    private function prepareErrorValuesForRawQuery(array $errorValues): array
    {
        // filter array only by allowed items
        $errorValues = array_intersect_key($errorValues, array_flip([
            'message',
            'code',
            'file',
            'line',
            'trace',
            'browser_info',
            'server_name',
            'request_uri',
            'root_page_uid',
            'crdate',
            'IP',
            'user',
            'user_id',
            'workspace',
            'event_dispatched',
        ]));

        foreach ($errorValues as $key => $value) {
            $errorValues[$key] = '\'' . addslashes((string) $value) . '\'';
        }

        return $errorValues;
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

<?php

declare(strict_types=1);

namespace RD\ErrorLog\Service\Database;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\Middleware as DriverMiddleware;
use Doctrine\DBAL\DriverManager;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Driver\PDOMySql\Driver as PDOMySqlDriver;
use TYPO3\CMS\Core\Database\Driver\PDOPgSql\Driver as PDOPgSqlDriver;
use TYPO3\CMS\Core\Database\Driver\PDOSqlite\Driver as PDOSqliteDriver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 *
 * This class is based on the methods taken from TYPO3\CMS\Core\Database\ConnectionPool
 *
 * The need for existing of this class is just one:
 *  - when any error happens before TYPO3 is booted completely, for example, something happens during the cache flush,
 *    it is going to be reported by our LogWriter. However, as TYPO3 is not ready at that moment, we should avoid ConnectionPool usage,
 *    as that is deprecated since #94979
 *    https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/11.4/Deprecation-94979-UsingCacheManagerOrDatabaseConnectionsDuringTYPO3Bootstrap.html
 *    Thus we replicate here it's functionality to create DB connection and execute SQL INSERT query "as raw as possible".
 */
final class ConnectionService
{
    private static $driverMap = [
        'pdo_mysql' => PDOMySqlDriver::class,
        'pdo_sqlite' => PDOSqliteDriver::class,
        'pdo_pgsql' => PDOPgSqlDriver::class,
    ];

    /**
     * @internal
     *
     * @param string $tableName
     *
     * @return Connection|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function getConnectionForTable(string $tableName): ?Connection
    {
        $connectionName = ConnectionPool::DEFAULT_CONNECTION_NAME;
        if (! empty($GLOBALS['TYPO3_CONF_VARS']['DB']['TableMapping'][$tableName])) {
            $connectionName = (string) $GLOBALS['TYPO3_CONF_VARS']['DB']['TableMapping'][$tableName];
        }

        if (empty($connectionName)) {
            return null;
        }

        $connectionParams = $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections'][$connectionName] ?? [];
        if (empty($connectionParams)) {
            return null;
        }

        if (empty($connectionParams['wrapperClass'])) {
            $connectionParams['wrapperClass'] = Connection::class;
        }

        if (! is_a($connectionParams['wrapperClass'], Connection::class, true)) {
            return null;
        }

        // Transform TYPO3 `tableoptions` to valid `doctrine/dbal` connection param option `defaultTableOptions`
        if (isset($connectionParams['tableoptions'])) {
            $connectionParams['defaultTableOptions'] = array_replace(
                $connectionParams['defaultTableOptions'] ?? [],
                $connectionParams['tableoptions']
            );
            unset($connectionParams['tableoptions']);
        }

        // Default to UTF-8 connection charset
        if (empty($connectionParams['charset'])) {
            $connectionParams['charset'] = 'utf8';
        }

        // if no custom driver is provided, map TYPO3 specific drivers
        if (! isset($connectionParams['driverClass']) && isset(self::$driverMap[$connectionParams['driver']])) {
            $connectionParams['driverClass'] = self::$driverMap[$connectionParams['driver']];
        }

        $middlewares = $this->getDriverMiddlewares($connectionParams);
        $configuration = $middlewares ? (new Configuration())->setMiddlewares($middlewares) : null;

        /** @var Connection $connection */
        $connection = DriverManager::getConnection($connectionParams, $configuration);
        $connection->prepareConnection($connectionParams['initCommands'] ?? '');
        return $connection;
    }

    private function getDriverMiddlewares(array $connectionParams): array
    {
        $middlewares = [];

        foreach ($connectionParams['driverMiddlewares'] ?? [] as $className) {
            if (! in_array(DriverMiddleware::class, class_implements($className) ?: [], true)) {
                continue;
            }
            $middlewares[] = GeneralUtility::makeInstance($className);
        }

        return $middlewares;
    }
}

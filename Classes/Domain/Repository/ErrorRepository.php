<?php

declare(strict_types=1);

namespace RD\ErrorLog\Domain\Repository;

use Exception;
use RD\ErrorLog\Domain\Model\Error;
use RD\ErrorLog\Domain\Model\Filter;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository as RepositoryAlias;

class ErrorRepository extends RepositoryAlias
{
    private const ERROR_LOG_TABLE = 'tx_errorlog_domain_model_error';

    public function getErrors(Filter $filter, bool $group = true)
    {
        $queryBuilder = $this->getQueryBuilderForTable(self::ERROR_LOG_TABLE);
        $queryConstraints = $this->createQueryConstraints($queryBuilder, $filter);
        if (!empty($queryConstraints)) {
            $queryBuilder->where(...$queryConstraints);
        }
        if ($group) {
            $queryBuilder->select('line', 'code', 'file');
        } else {
            $queryBuilder->select('*');
        }

        $queryBuilder->from(self::ERROR_LOG_TABLE);
        if ($group) {
            $queryBuilder
                ->addSelectLiteral('MAX(message) AS message')
                ->addSelectLiteral('MAX(uid) AS uid')
                ->addSelectLiteral('MIN(crdate) AS first_occurrence')
                ->addSelectLiteral('MAX(crdate) AS last_occurrence')
                ->addSelectLiteral('COUNT(*) AS count')
                ->orderBy('last_occurrence', 'DESC')
                ->groupBy('code', 'line', 'file');
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    protected function createQueryConstraints(QueryBuilder $queryBuilder, Filter $filter): array
    {
        $queryConstraints = [];

        if ($filter->getStart()) {
            $queryConstraints[] = $queryBuilder->expr()->gte('crdate', $queryBuilder->createNamedParameter($filter->getStartTimeStamp(), \PDO::PARAM_INT));
        }

        if ($filter->getEnd()) {
            $queryConstraints[] = $queryBuilder->expr()->lt('crdate', $queryBuilder->createNamedParameter($filter->getEndTimestamp(), \PDO::PARAM_INT));
        }

        if ($filter->getSearch() !== '') {
            $queryConstraints[] =
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->like('code', $queryBuilder->createNamedParameter('%' . $filter->getSearch() . '%', \PDO::PARAM_STR)),
                    $queryBuilder->expr()->like('message', $queryBuilder->createNamedParameter('%' . $filter->getSearch() . '%', \PDO::PARAM_STR)),
                    $queryBuilder->expr()->like('file', $queryBuilder->createNamedParameter('%' . $filter->getSearch() . '%', \PDO::PARAM_STR)),
                );
        }

        if ($filter->getEventDispatched() === false) {
            $queryConstraints[] = $queryBuilder->expr()->eq('event_dispatched', $queryBuilder->createNamedParameter($filter->getEventDispatched(), \PDO::PARAM_INT));
        }

        $queryBuilder->orderBy('crdate', 'DESC');

        if ($filter->getRootPage()) {
            $queryConstraints[] = $queryBuilder->expr()->eq('root_page_uid', $queryBuilder->createNamedParameter($filter->getRootPage(), \PDO::PARAM_INT));
        }

        return $queryConstraints;
    }

    public function getErrorsByUid(int $uid)
    {
        $queryBuilder = $this->getQueryBuilderForTable(self::ERROR_LOG_TABLE);
        $error = $queryBuilder
            ->select('*')
            ->from(self::ERROR_LOG_TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($error) {
            return $queryBuilder
                ->select('*')
                ->from(self::ERROR_LOG_TABLE)
                ->where(
                    $queryBuilder->expr()->eq('code', $queryBuilder->createNamedParameter($error['code'], \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('line', $queryBuilder->createNamedParameter($error['line'], \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('file', $queryBuilder->createNamedParameter($error['file'], \PDO::PARAM_STR))
                )
                ->orderBy('crdate', 'DESC')
                ->executeQuery()
                ->fetchAllAssociative();
        }

        return null;
    }

    public function deleteByUid($uid): ?int
    {
        $result = null;
        $queryBuilder = $this->getQueryBuilderForTable(self::ERROR_LOG_TABLE);

        $error = $queryBuilder
            ->select('*')
            ->from(self::ERROR_LOG_TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        $queryBuilder = $this->getQueryBuilderForTable(self::ERROR_LOG_TABLE);

        if ($error) {
            $queryBuilder
                ->where(
                    $queryBuilder->expr()->eq('code', $queryBuilder->createNamedParameter($error['code'], \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('line', $queryBuilder->createNamedParameter($error['line'], \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('file', $queryBuilder->createNamedParameter($error['file'], \PDO::PARAM_STR))
                );
              $result = $queryBuilder->delete(self::ERROR_LOG_TABLE)->executeStatement();
        }

        return $result;
    }

    public function getRootPages()
    {
        $queryBuilder = $this->getQueryBuilderForTable(self::ERROR_LOG_TABLE);
        return $queryBuilder
            ->select('root_page_uid')
            ->from(self::ERROR_LOG_TABLE)
            ->groupBy('root_page_uid')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function isFirstOccurrence(string $errorTypeHash): bool
    {
        $queryBuilder = $this->getQueryBuilderForTable('tx_errorlog_hashes');
        $count = $queryBuilder
            ->count('*')
            ->from('tx_errorlog_hashes')
            ->where(
                $queryBuilder->expr()->eq('error_hash', $queryBuilder->createNamedParameter($errorTypeHash))
            )
            ->executeQuery()->fetchOne();

        return $count === 0;
    }

    public function createOccurrence(string $errorTypeHash, int $errorUid): void
    {
        $queryBuilder = $this->getConnectionForTable('tx_errorlog_hashes');
        $queryBuilder
            ->insert(
                'tx_errorlog_hashes',
                [
                    'error_hash' => $errorTypeHash,
                    'error_uid' => $errorUid,
                    'occurred_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ]
            );
    }

    public function deleteErrorsOlderThan(int $days): void
    {
        $queryBuilder = $this->getQueryBuilderForTable(self::ERROR_LOG_TABLE);
        $uids = $queryBuilder->select('uid')
            ->from(self::ERROR_LOG_TABLE)
            ->where(
                $queryBuilder->expr()->lte('crdate', $queryBuilder->createNamedParameter(time() - $days * 24 * 60 * 60, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $queryBuilder
            ->delete(self::ERROR_LOG_TABLE)
            ->where(
                $queryBuilder->expr()->in('uid', array_column($uids, 'uid'))
            )
            ->executeQuery();

        $queryBuilder = $this->getQueryBuilderForTable('tx_errorlog_hashes');
        $queryBuilder
            ->delete('tx_errorlog_hashes')
            ->where(
                $queryBuilder->expr()->in('error_uid', array_column($uids, 'uid'))
            )
            ->executeQuery();
    }

    public function gerErrorsReport(int $seconds)
    {
        $filter = new Filter();
        $filter->setStart(date('Y-m-d H:i:s', time() - $seconds));
        $filter->setEnd(date('Y-m-d H:i:s', time()));
        return $this->getErrors($filter);
    }

    public function getQueryBuilderForTable(string $table): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table)->createQueryBuilder();
    }

    public function getConnectionForTable(string $table): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
    }

    public function generateErrorTypeHash($error): string
    {
        if ($error instanceof Error) {
            return hash('sha1', $error->getCode() . $error->getLine() . $error->getFile());
        }

        if (is_array($error)) {
            return hash('sha1', $error['code'] . $error['line'] . $error['file']);
        }

        throw new Exception('Invalid argument type');
    }

    public function setDispatchedEventForErrors(array $uids)
    {
        $queryBuilder = $this->getQueryBuilderForTable(self::ERROR_LOG_TABLE);
        $queryBuilder
            ->update(self::ERROR_LOG_TABLE)
            ->set('event_dispatched', 1)
            ->where(
                $queryBuilder->expr()->in('uid', $uids)
            )
            ->executeStatement();
    }
}

<?php
declare(strict_types=1);

namespace WebentwicklerAt\RecordEvents\Service;

/*
 * This file is part of the record_events extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebentwicklerAt\RecordEvents\Event\RecordEndedEvent;
use WebentwicklerAt\RecordEvents\Event\RecordStartedEvent;

class Enablecolumns
{
    protected ConnectionPool $connectionPool;

    protected EventDispatcher $eventDispatcher;

    protected Registry $registry;

    protected array $lastRunInformation = [];

    public function __construct()
    {
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->eventDispatcher = GeneralUtility::makeInstance(EventDispatcher::class);
        $this->registry = GeneralUtility::makeInstance(Registry::class);
    }

    public function monitor()
    {
        $this->getLastRunInformation();
        $start = (int)$this->lastRunInformation['start'] ?: $GLOBALS['EXEC_TIME'];
        $end = $GLOBALS['EXEC_TIME'];

        $tableNames = $this->getMonitoredTableNames();
        foreach ($tableNames as $tableName) {
            $starttimeFieldName = $GLOBALS['TCA'][$tableName]['ctrl']['enablecolumns']['starttime'];
            if ($starttimeFieldName) {
                $recordUids = $this->getRecordUidsBetween($tableName, $starttimeFieldName, $start, $end);
                if (count($recordUids)) {
                    $this->eventDispatcher->dispatch(
                        new RecordStartedEvent($tableName, $recordUids)
                    );
                }
            }

            $endtimeFieldName = $GLOBALS['TCA'][$tableName]['ctrl']['enablecolumns']['endtime'];
            if ($endtimeFieldName) {
                $recordUids = $this->getRecordUidsBetween($tableName, $endtimeFieldName, $start, $end);
                if (count($recordUids)) {
                    $this->eventDispatcher->dispatch(
                        new RecordEndedEvent($tableName, $recordUids)
                    );
                }
            }
        }

        $this->setLastRunInformation();
    }

    protected function getLastRunInformation(): void
    {
        $this->lastRunInformation = $this->registry->get('tx_recordevents', 'lastRun', []);
    }

    protected function setLastRunInformation(): void
    {
        $this->registry->set('tx_recordevents', 'lastRun', [
            'start' => $GLOBALS['EXEC_TIME'],
            'end' => time(),
        ]);
    }

    protected function getMonitoredTableNames(): array
    {
        $tableNames = array_keys($GLOBALS['TCA']);

        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $blacklist = (string)$extensionConfiguration->get('record_events', 'blacklist');
        $blacklist = GeneralUtility::trimExplode(',', $blacklist, true);
        $whitelist = (string)$extensionConfiguration->get('record_events', 'whitelist');
        $whitelist = GeneralUtility::trimExplode(',', $whitelist, true);

        $tableNames = array_filter($tableNames, function($table) use ($blacklist, $whitelist) {
            return (
                !$this->inWildcardList($blacklist, $table)
                && $this->inWildcardList($whitelist, $table)
            );
        });

        return $tableNames;
    }

    protected function inWildcardList(array $list, string $item): bool
    {
        foreach ($list as $pattern) {
            if (fnmatch($pattern, $item)) {
                return true;
            }
        }

        return false;
    }

    protected function getRecordUidsBetween(string $tableName, string $fieldName, int $start, int $end): array
    {
        $queryBuilder = $this->getQueryBuilder($tableName);
        $result = $queryBuilder
            ->select('uid')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->gt($fieldName, $start),
                $queryBuilder->expr()->lte($fieldName, $end)
            )
            ->executeQuery();

        $rows = $result->fetchAllAssociative();
        $uids = array_column($rows, 'uid');

        return $uids;
    }

    protected function getQueryBuilder(string $tableName)
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($tableName);
        $queryBuilder->getRestrictions()
            ->removeByType(StartTimeRestriction::class)
            ->removeByType(EndTimeRestriction::class);

        return $queryBuilder;
    }
}

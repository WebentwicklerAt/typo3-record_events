<?php
declare(strict_types=1);

namespace WebentwicklerAt\RecordEvents\Event;

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

abstract class RecordEnablecolumnsEvent implements RecordEnablecolumnsEventInterface
{
    private string $tableName;
    private array $recordUids;

    public function __construct(string $tableName, array $recordUids)
    {
        $this->tableName = $tableName;
        $this->recordUids = $recordUids;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getRecordUids(): array
    {
        return $this->recordUids;
    }
}

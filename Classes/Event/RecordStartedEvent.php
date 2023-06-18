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

final class RecordStartedEvent extends RecordEnablecolumnsEvent implements RecordEnablecolumnsEventInterface
{
    public function __construct(string $tableName, array $recordUids)
    {
        parent::__construct($tableName, $recordUids);
    }
}

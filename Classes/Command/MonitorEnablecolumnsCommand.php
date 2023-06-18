<?php
declare(strict_types=1);

namespace WebentwicklerAt\RecordEvents\Command;

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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebentwicklerAt\RecordEvents\Service\Enablecolumns;

class MonitorEnablecolumnsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $enablecolumnsService = GeneralUtility::makeInstance(Enablecolumns::class);
            $enablecolumnsService->monitor();
        } catch (\Exception $exception) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

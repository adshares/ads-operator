<?php

/**
 * Copyright (c) 2018-2022 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types=1);

namespace Adshares\AdsOperator\Command;

use Adshares\AdsOperator\AdsImporter\Exception\AdsImporterException;
use Adshares\AdsOperator\AdsImporter\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateSnapshotCommand extends Command
{
    use LockableTrait;

    private Importer $adsImporter;

    public function __construct(Importer $adsImporter)
    {
        $this->adsImporter = $adsImporter;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ads:snapshots:create')
            ->setDescription('Create snapshot')
            ->addArgument(
                'blockId',
                InputArgument::IS_ARRAY,
                'Block ID to calculate snapshot'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force to create snapshot'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->lock()) {
            $io->error('The command is already running in another process.');
            return 1;
        }

        $blockIds = $input->getArgument('blockId');
        $force = (bool)$input->getOption('force');

        try {
            $snapshots = $this->adsImporter->calculateSnapshots($blockIds, $force);
        } catch (AdsImporterException $exception) {
            $io->error(sprintf('Snapshot calculation error: %s', $exception->getMessage()));
            return 1;
        }

        if (empty($snapshots)) {
            $io->comment('Nothing to calculate');
        } else {
            $io->success(sprintf('Snapshots %s calculated successfully', implode(', ', $snapshots)));
        }

        return 0;
    }
}

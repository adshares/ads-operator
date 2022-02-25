<?php
/**
 * Copyright (C) 2018 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator.  If not, see <https://www.gnu.org/licenses/>
 */

namespace Adshares\AdsOperator\Command;

use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\AdsImporter\Importer;
use Adshares\AdsOperator\AdsImporter\ImporterResult;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to import data from ADS network.
 *
 * @package Adshares\AdsOperator\Command
 */
class DividendCommand extends ContainerAwareCommand
{
    use LockableTrait;
    /**
     * @var Importer
     */
    private $adsImporter;

    /**
     * ImportADSCommand constructor.
     *
     * @param Importer $adsImporter
     */
    public function __construct(Importer $adsImporter)
    {
        $this->adsImporter = $adsImporter;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('ads:dividend')
            ->setDescription('Calculate missing dividend transactions');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 1;
        }

        try {
            $this->adsImporter->loadGenesis($this->getContainer()->getParameter('kernel.project_dir'));
            $result = $this->adsImporter->calculateDividends();
        } catch (AdsClientException $ex) {
            $output->writeln(sprintf('Dividend calculation error: %s', $ex->getMessage()));
            return 1;
        }

        $output->writeln(print_r($result, true));
        return 0;
    }
}

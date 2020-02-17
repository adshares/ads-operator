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

declare(strict_types = 1);

namespace Adshares\AdsOperator\Command;

use Adshares\AdsOperator\Exchange\Exception\CalculationMethodRuntimeException;
use Adshares\AdsOperator\Repository\Exception\ExchangeRateNotFoundException;
use Adshares\AdsOperator\UseCase\Exchange\CalculateInternalExchangeRate;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CalculateInternalExchangeRateCommand extends ContainerAwareCommand
{
    private const CALCULATION_HOUR_PERIOD = 1; // 1 hour

    /** @var CalculateInternalExchangeRate */
    private $useCase;

    public function __construct(CalculateInternalExchangeRate $useCase)
    {
        $this->useCase = $useCase;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $date = new DateTime('-1 hour');
        $date->setTime((int)$date->format('H'), 0);

        $this
            ->setName('ops:exchange:calculate')
            ->setDescription('Importing exchange rate from provider')
            ->addArgument(
                'currencies',
                InputArgument::IS_ARRAY,
                'Which currencies do you want to import',
                explode(',', $_ENV['EXCHANGE_CURRENCIES'] ?? '')
            )
            ->addOption(
                'date',
                'd',
                InputOption::VALUE_REQUIRED,
                'Date for',
                $date->format(DateTime::ATOM)
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $startDate = DateTime::createFromFormat(DateTime::ATOM, $input->getOption('date'));
        if (!$startDate) {
            $io->error(sprintf('Start Date (%s) is not valid.', $input->getOption('date')));

            return 1;
        }

        $currencies = $input->getArgument('currencies');
        if (empty($currencies)) {
            $io->warning('Currencies list is empty.');
        }

        $startDate->setTime((int)$startDate->format('H'), 0);
        $endDate = (clone $startDate)->modify(sprintf('+%d hour', self::CALCULATION_HOUR_PERIOD));
        $io->comment(
            sprintf(
                'Calculating hourly currency rate for %s to %s',
                $startDate->format('Y-m-d H:i'),
                $endDate->format('Y-m-d H:i')
            )
        );

        foreach ($currencies as $currency) {
            try {
                $io->comment(sprintf('Starting calculating for %s', $currency));
                $this->useCase->calculate($startDate, $endDate, $currency);
                $io->success('Finished calculating an internal rate');
            } catch (CalculationMethodRuntimeException|ExchangeRateNotFoundException $exception) {
                $io->error(sprintf('Error: %s', $exception->getMessage()));
            }
        }

        return 0;
    }
}

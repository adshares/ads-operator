<?php

/**
 * Copyright (c) 2018-2023 Adshares sp. z o.o.
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

use Adshares\AdsOperator\Exchange\Exception\ProviderRuntimeException;
use Adshares\AdsOperator\Exchange\Provider\Provider;
use Adshares\AdsOperator\UseCase\Exchange\UpdateExchangeRate;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FetchExchangeRateCommand extends ContainerAwareCommand
{
    use LockableTrait;

    private const COIN_GECKO = 'coin_gecko';
    private const CMC = 'cmc';

    private const SUPPORTED_PROVIDERS = Provider::PROVIDER_LIST;

    /** @var UpdateExchangeRate */
    private $useCase;

    public function __construct(UpdateExchangeRate $useCase)
    {
        $this->useCase = $useCase;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('ops:exchange:import')
            ->setDescription('Importing exchange rate from provider')
            ->addArgument(
                'currencies',
                InputArgument::IS_ARRAY,
                'Which currencies do you want to import',
                explode(',', $_ENV['EXCHANGE_CURRENCIES'] ?? '')
            )
            ->addOption(
                'provider',
                'p',
                InputOption::VALUE_REQUIRED,
                'Which provider do you want to use',
                self::CMC
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $io = new SymfonyStyle($input, $output);

        $providerName = $input->getOption('provider');
        if (!array_key_exists($providerName, self::SUPPORTED_PROVIDERS)) {
            $io->error(sprintf('Provider `%s` is not supported.', $providerName));

            return 1;
        }

        $currencies = $input->getArgument('currencies');
        if (empty($currencies)) {
            $io->warning('Currencies list is empty.');
        }

        foreach ($currencies as $currency) {
            try {
                $io->comment(sprintf('Starting importing an exchange rate for %s from %s', $currency, $providerName));
                $this->useCase->update(new DateTime(), $providerName, $currency);
                $io->success('Finished importing an exchange rate');
            } catch (ProviderRuntimeException $exception) {
                $io->error(sprintf('Error: %s', $exception->getMessage()));
            }
        }

        return 0;
    }
}

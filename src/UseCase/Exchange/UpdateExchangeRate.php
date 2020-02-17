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

namespace Adshares\AdsOperator\UseCase\Exchange;

use Adshares\AdsOperator\Document\ExchangeRateHistory;
use Adshares\AdsOperator\Exchange\Provider\Provider;
use Adshares\AdsOperator\Repository\Exception\ExchangeRateNotFoundException;
use Adshares\AdsOperator\Repository\ExchangeRateHistoryRepositoryInterface;
use DateTime;

final class UpdateExchangeRate
{
    /** @var ExchangeRateHistoryRepositoryInterface */
    private $repository;

    /** @var Provider */
    private $provider;

    public function __construct(
        ExchangeRateHistoryRepositoryInterface $repository,
        Provider $provider
    ) {
        $this->repository = $repository;
        $this->provider = $provider;
    }

    public function update(DateTime $dateTime, string $providerName, string $currency): void
    {
        $currency = strtolower($currency);
        $provider = $this->provider->get($providerName);
        $newestFromProvider = $provider->fetchExchangeRate($dateTime, $currency);

        try {
            $newestFromRepository = $this->repository->fetchNewest();
        } catch (ExchangeRateNotFoundException $exception) {
            $newestFromRepository = null;
        }

        if (null === $newestFromRepository || $newestFromProvider->getDate() > $newestFromRepository->getDate()) {
            $exchangeRate = new ExchangeRateHistory(
                $newestFromProvider->getDate(),
                $newestFromProvider->getRate(),
                $providerName,
                $currency
            );

            $this->repository->addExchangeRate($exchangeRate);
        }
    }
}

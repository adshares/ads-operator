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

declare(strict_types=1);


namespace Adshares\AdsOperator\UseCase\Exchange;

use Adshares\AdsOperator\Document\ExchangeRate as exchangeRateDocument;
use Adshares\AdsOperator\Document\ExchangeRateHistory;
use Adshares\AdsOperator\Exchange\Calculation\CalculationMethodInterface;
use Adshares\AdsOperator\Exchange\Dto\ExchangeRate;
use Adshares\AdsOperator\Exchange\Dto\ExchangeRateCollection;
use Adshares\AdsOperator\Repository\ExchangeRateHistoryRepositoryInterface;
use Adshares\AdsOperator\Repository\ExchangeRateRepositoryInterface;
use DateTime;

class CalculateInternalExchangeRate
{
    /** @var ExchangeRateHistoryRepositoryInterface */
    private $exchangeRateHis;
    /** @var CalculationMethodInterface */
    private $calculationMethod;
    /** @var float */
    private $rate;
    /** @var ExchangeRateRepositoryInterface */
    private $exchangeRateRepository;

    public function __construct(
        ExchangeRateHistoryRepositoryInterface $exchangeRateHistoryRepository,
        ExchangeRateRepositoryInterface $exchangeRateRepository,
        CalculationMethodInterface $calculationMethod
    ) {
        $this->exchangeRateHis = $exchangeRateHistoryRepository;
        $this->calculationMethod = $calculationMethod;
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    public function calculate(DateTime $start, DateTime $end, string $currency): void
    {
        $currency = strtolower($currency);
        $exchangeRatesHistory = $this->exchangeRateHis->fetchForCurrencyBetweenDates($currency, $start, $end);

        $collection = new ExchangeRateCollection();

        /** @var ExchangeRateHistory $item */
        foreach ($exchangeRatesHistory as $item) {
            $collection->add(new ExchangeRate($item->getDate(), $item->getRate(), $item->getCurrency()));
        }

        $rate = $this->calculationMethod->calculate($collection);

        $this->exchangeRateRepository->addExchangeRate(new exchangeRateDocument($start, $rate, $currency));

        $this->rate = $rate;
    }

    public function getRateValue(): ?float
    {
        return $this->rate;
    }
}

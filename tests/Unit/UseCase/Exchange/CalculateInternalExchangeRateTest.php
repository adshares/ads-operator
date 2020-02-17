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

namespace Adshares\AdsOperator\Tests\Unit\UseCase\Exchange;

use Adshares\AdsOperator\Document\ExchangeRate;
use Adshares\AdsOperator\Exchange\Calculation\CalculationMedianMethod;
use Adshares\AdsOperator\Exchange\Exception\CalculationMethodRuntimeException;
use Adshares\AdsOperator\Repository\ExchangeRateHistoryRepositoryInterface;
use Adshares\AdsOperator\Repository\ExchangeRateRepositoryInterface;
use Adshares\AdsOperator\UseCase\Exchange\CalculateInternalExchangeRate;
use DateTime;
use PHPUnit\Framework\TestCase;

final class CalculateInternalExchangeRateTest extends TestCase
{
    public function testWhenNoDataBetweenDatesShouldThrowException(): void
    {
        $this->expectException(CalculationMethodRuntimeException::class);

        $exchangeRateHistoryRepository = $this->createMock(ExchangeRateHistoryRepositoryInterface::class);
        $exchangeRateHistoryRepository
            ->expects($this->once())
            ->method('fetchForCurrencyBetweenDates')
            ->willReturn([]);

        $useCase = new CalculateInternalExchangeRate(
            $exchangeRateHistoryRepository,
            $this->createMock(ExchangeRateRepositoryInterface::class),
            new CalculationMedianMethod()
        );

        $useCase->calculate(new DateTime(), new DateTime(), 'usd');
    }

    public function testWhenDataExistsInHistoryRepositoryShouldStoreCalculation(): void
    {
        $data = [
            new ExchangeRate(new DateTime(), 1, 'usd'),
            new ExchangeRate(new DateTime(), 2, 'usd'),
            new ExchangeRate(new DateTime(), 3, 'usd'),
            new ExchangeRate(new DateTime(), 4, 'usd'),
            new ExchangeRate(new DateTime(), 5, 'usd'),
            new ExchangeRate(new DateTime(), 6, 'usd'),
            new ExchangeRate(new DateTime(), 7, 'usd'),
            new ExchangeRate(new DateTime(), 8, 'usd'),
            new ExchangeRate(new DateTime(), 9, 'usd'),
        ];

        $exchangeRateHistoryRepository = $this->createMock(ExchangeRateHistoryRepositoryInterface::class);
        $exchangeRateHistoryRepository
            ->expects($this->once())
            ->method('fetchForCurrencyBetweenDates')
            ->willReturn($data);

        $exchangeRateRepository = $this->createMock(ExchangeRateRepositoryInterface::class);
        $exchangeRateRepository
            ->expects($this->once())
            ->method('addExchangeRate');

        $useCase = new CalculateInternalExchangeRate(
            $exchangeRateHistoryRepository,
            $exchangeRateRepository,
            new CalculationMedianMethod()
        );

        $useCase->calculate(new DateTime(), new DateTime(), 'usd');

        $this->assertNotNull($useCase->getRateValue());
    }
}

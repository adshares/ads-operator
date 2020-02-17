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

use Adshares\AdsOperator\Document\ExchangeRateHistory;
use Adshares\AdsOperator\Exchange\Dto\ExchangeRate;
use Adshares\AdsOperator\Exchange\Provider\Client\ClientInterface;
use Adshares\AdsOperator\Exchange\Provider\Provider;
use Adshares\AdsOperator\Repository\Exception\ExchangeRateNotFoundException;
use Adshares\AdsOperator\Repository\ExchangeRateHistoryRepositoryInterface;
use Adshares\AdsOperator\UseCase\Exchange\UpdateExchangeRate;
use DateTime;
use PHPUnit\Framework\TestCase;

final class UpdateExchangeRateTest extends TestCase
{
    public function testWhenRepositoryThrowsNotFoundExceptionThanAddExchangeRateIsCalled(): void
    {
        $repository = $this->createMock(ExchangeRateHistoryRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('fetchNewest')
            ->willThrowException(new ExchangeRateNotFoundException());

        $repository
            ->expects($this->once())
            ->method('addExchangeRate');

        $dateTime = new DateTime();
        $useCase = new UpdateExchangeRate($repository, $this->mockProvider($dateTime));
        $useCase->update(new DateTime(), 'coin_gecko', 'USD');
    }

    public function testWhenDateFromRepositoryIsGreaterThanProviderDateThanAddExchangeRateIsNotCalled(): void
    {
        $repository = $this->createMock(ExchangeRateHistoryRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('fetchNewest')
            ->willReturn(new ExchangeRateHistory(new DateTime(), 0.05, 'coin_gecko'));

        $repository
            ->expects($this->never())
            ->method('addExchangeRate');

        $dateTime = new DateTime('-1 hour');
        $useCase = new UpdateExchangeRate($repository, $this->mockProvider($dateTime));
        $useCase->update(new DateTime(), 'coin_gecko', 'USD');
    }

    public function testWhenDateFromRepositoryIsSmallerThanProviderDateThanAddExchangeRateIsCalled(): void
    {
        $repository = $this->createMock(ExchangeRateHistoryRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('fetchNewest')
            ->willReturn(new ExchangeRateHistory(new DateTime('-1 hour'), 0.05, 'coin_gecko'));

        $repository
            ->expects($this->once())
            ->method('addExchangeRate');

        $dateTime = new DateTime();
        $useCase = new UpdateExchangeRate($repository, $this->mockProvider($dateTime));
        $useCase->update(new DateTime(), 'coin_gecko', 'USD');
    }

    private function mockProvider(DateTime $dateTime, ?ExchangeRate $exchangeRate = null)
    {
        if (!$exchangeRate) {
            $exchangeRate = new ExchangeRate($dateTime, 0.06, 'USD');
        }
        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects($this->once())
            ->method('fetchExchangeRate')
            ->willReturn($exchangeRate);

        $provider = $this->createMock(Provider::class);
        $provider
            ->expects($this->once())
            ->method('get')
            ->willReturn($client);

        return $provider;
    }
}

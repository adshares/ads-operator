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

namespace Adshares\AdsOperator\Tests\Unit\Exchange\Calculation;

use Adshares\AdsOperator\Exchange\Calculation\CalculationMedianMethod;
use Adshares\AdsOperator\Exchange\Dto\ExchangeRate;
use Adshares\AdsOperator\Exchange\Dto\ExchangeRateCollection;
use DateTime;
use PHPUnit\Framework\TestCase;

final class CalculationMedianMethodTest extends TestCase
{
    public function testCalculation(): void
    {
        $data = [
            new ExchangeRate(new DateTime(), 22, 'usd'),
            new ExchangeRate(new DateTime(), 1, 'usd'),
            new ExchangeRate(new DateTime(), 2, 'usd'),
            new ExchangeRate(new DateTime(), 3, 'usd'),
            new ExchangeRate(new DateTime(), 4, 'usd'),
            new ExchangeRate(new DateTime(), 5, 'usd'),
            new ExchangeRate(new DateTime(), 6, 'usd'),
            new ExchangeRate(new DateTime(), 7, 'usd'),
            new ExchangeRate(new DateTime(), 8, 'usd'),
            new ExchangeRate(new DateTime(), 8, 'usd'),
        ];

        $collection = new ExchangeRateCollection($data);

        $calculation = new CalculationMedianMethod();
        $result = $calculation->calculate($collection);

        $this->assertEquals(5, $result);
    }
}

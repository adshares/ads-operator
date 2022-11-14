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

namespace Adshares\AdsOperator\Tests\Unit\Exchange\Provider;

use Adshares\AdsOperator\Exchange\Provider\Client\CoinGecko;
use Adshares\AdsOperator\Exchange\Provider\Provider;
use PHPUnit\Framework\TestCase;
use Adshares\AdsOperator\Exchange\Exception\ProviderRuntimeException;

class ProviderTest extends TestCase
{
    public function testWhenProviderDoesNotExist(): void
    {
        $this->expectException(ProviderRuntimeException::class);
        $this->expectExceptionMessage('Provider unknown_provider is not supported.');

        $provider = new Provider([]);

        $provider->get('unknown_provider');
    }

    public function testWhenProviderIsNotConfigured(): void
    {
        $this->expectException(ProviderRuntimeException::class);
        $this->expectExceptionMessage('Provider coin_gecko is not configured.');

        $provider = new Provider([]);

        $provider->get('coin_gecko');
    }

    public function testWhenProviderExistsAndIsConfigured(): void
    {
        $providers = [
            new CoinGecko('', '', 5)
        ];

        $provider = new Provider($providers);
        $provider->get('coin_gecko');

        $this->addToAssertionCount(1);
    }
}

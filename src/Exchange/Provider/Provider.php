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


namespace Adshares\AdsOperator\Exchange\Provider;

use Adshares\AdsOperator\Exchange\Provider\Client\ClientInterface;
use Adshares\AdsOperator\Exchange\Provider\Client\CoinGecko;
use Adshares\AdsOperator\Exchange\Exception\ProviderRuntimeException;
use function array_key_exists;

class Provider
{
    private const PROVIDER_LIST = [
        'coin_gecko' => CoinGecko::class,
    ];

    private $providers;

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function get(string $providerName): ClientInterface
    {
        if (array_key_exists($providerName, self::PROVIDER_LIST)) {
            return $this->getProvider($providerName);
        }

        throw new ProviderRuntimeException(sprintf('Provider %s is not supported.', $providerName));
    }

    private function getProvider(string $providerName): ClientInterface
    {
        foreach ($this->providers as $provider) {
            if (get_class($provider) === self::PROVIDER_LIST[$providerName]) {
                return $provider;
            }
        }

        throw new ProviderRuntimeException(sprintf('Provider %s is not configured.', $providerName));
    }
}

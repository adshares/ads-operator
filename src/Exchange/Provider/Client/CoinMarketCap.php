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

namespace Adshares\AdsOperator\Exchange\Provider\Client;

use Adshares\AdsOperator\Exchange\Dto\ExchangeRate;
use Adshares\AdsOperator\Exchange\Exception\ProviderRuntimeException;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

use function json_decode;
use function strtolower;

class CoinMarketCap implements ClientInterface
{
    /** @var string */
    private $serviceUrl;
    /** @var int */
    private $timeout;
    /** @var string */
    private $key;

    public function __construct(string $serviceUrl, string $key, int $timeout)
    {
        $this->serviceUrl = $serviceUrl;
        $this->timeout = $timeout;
        $this->key = $key;
    }

    public function fetchExchangeRate(DateTime $date, string $currency): ExchangeRate
    {
        $currency = strtoupper($currency);
        $client = new Client($this->requestParameters());

        try {
            $uri = sprintf('%s/v2/tools/price-conversion?amount=1&symbol=ADS&&convert=%s', $this->serviceUrl, $currency);
            $response = $client->get($uri);
        } catch (RequestException $exception) {
            throw new ProviderRuntimeException(
                sprintf('Could not connect to %s (%s).', $this->serviceUrl, $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }

        $statusCode = $response->getStatusCode();
        $body = (string)$response->getBody();

        $this->validateResponse($statusCode, $body, $currency);
        $decoded = json_decode($body, true);
        $data = reset($decoded['data']);

        return new ExchangeRate($date, $data['quote'][$currency]['price'], $currency);
    }

    private function requestParameters(): array
    {
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
                'X-CMC_PRO_API_KEY' => $this->key,
            ],
            'timeout' => $this->timeout,
        ];

        return $params;
    }

    private function validateResponse(int $statusCode, string $body, string $currency): void
    {
        if ($statusCode !== Response::HTTP_OK) {
            throw new ProviderRuntimeException(sprintf('Unexpected response code `%s`.', $statusCode));
        }

        if (empty($body)) {
            throw new ProviderRuntimeException('Empty list');
        }

        $decoded = json_decode($body, true);

        if (!isset($decoded['data'])) {
            throw new ProviderRuntimeException(sprintf('Unsupported response format (%s)', $body));
        }
    }
}

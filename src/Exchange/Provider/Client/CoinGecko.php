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


namespace Adshares\AdsOperator\Exchange\Provider\Client;

use Adshares\AdsOperator\Exchange\Dto\ExchangeRate;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use RuntimeException;
use function strtolower;
use Symfony\Component\HttpFoundation\Response;
use Adshares\AdsOperator\Exchange\Exception\ProviderRuntimeException;
use function json_decode;

class CoinGecko implements ClientInterface
{
    /** @var string */
    private $serviceUrl;
    /** @var int */
    private $timeout;
    /** @var string */
    private $id;
    /** @var string */
    private $currency;

    public function __construct(string $serviceUrl, string $id, string $currency, int $timeout)
    {
        $this->serviceUrl = $serviceUrl;
        $this->timeout = $timeout;
        $this->id = $id;
        $this->currency = strtolower($currency);
    }

    public function fetchExchangeRate(DateTime $date): ExchangeRate
    {
        $client = new Client($this->requestParameters());

        try {
            $uri = sprintf('%s/simple/price?ids=%s&vs_currencies=%s', $this->serviceUrl, $this->id, $this->currency);
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

        $this->validateResponse($statusCode, $body);
        $decoded = json_decode($body, true);

        return new ExchangeRate($date, $decoded['adshares'][$this->currency], $this->currency);
    }

    private function requestParameters(): array
    {
        $params = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache',
            ],
            'timeout' => $this->timeout,
        ];

        return $params;
    }

    private function validateResponse(int $statusCode, string $body): void
    {
        if ($statusCode !== Response::HTTP_OK) {
            throw new ProviderRuntimeException(sprintf('Unexpected response code `%s`.', $statusCode));
        }

        if (empty($body)) {
            throw new ProviderRuntimeException('Empty list');
        }

        $decoded = json_decode($body, true);

        if (!isset($decoded['adshares'][$this->currency])) {
            throw new ProviderRuntimeException(sprintf('Unsupported response format (%s)', $body));
        }
    }
}

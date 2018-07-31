<?php
/**
 * Copyright (C) 2018 Adshares sp. z. o.o.
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

use Behat\Behat\Context\Context;
use GuzzleHttp\ClientInterface;
use Behat\Gherkin\Node\PyStringNode;

class ApiContext implements Context
{
    const API_VERSION = 'v1';

    private $client;
    private $method;
    private $sort;
    private $order;
    private $limit;
    private $offset;
    private $resource;
    private $body;
    private $response;

    public function __construct(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @Given I want to get the list of :url
     */
    public function iWantToGetTheListOf($resource)
    {
        $this->resource = $resource;
        $this->method = 'GET';
    }

    /**
     * @Given I want to get the resource :url with id :id
     */
    public function iWantToGetTheResource($resource, $id)
    {
        $this->resource = "${resource}/${id}";
        $this->method = 'GET';
    }

    /**
     * @Given I want to sort by :value
     */
    public function iWantToSortBy(string $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @Given I want to order by :value
     */
    public function iWantToOrderBy(string $order)
    {
        $this->order = $order;
    }

    /**
     * @Given I want to limit to :value
     */
    public function iWantToLimitTo(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @Given I want to offset to :value
     */
    public function iWantToOffsetTo(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @When I request resource
     */
    public function iRequestResource()
    {
        if (!$this->resource) {
            throw new \Exception('Resource needs to be set to perform request');
        }
        $url = sprintf('/api/%s/%s', self::API_VERSION, $this->resource);
        $options = ['http_errors' => false];

        $data = [];
        if (null !== $this->sort) {
            $data['sort'] = $this->sort;
        }

        if (null !== $this->order) {
            $data['order'] = $this->order;
        }

        if (null !== $this->limit) {
            $data['limit'] = $this->limit;
        }

        if (null !== $this->offset) {
            $data['offset'] = $this->offset;
        }

        if (!empty($data)) {
            $url = sprintf('%s?%s', $url, http_build_query($data));
        }

        if (null !== $this->body) {
            $options['body'] = $this->body;
        }

        $this->response = $this->client->request(
            $this->method,
            $url,
            $options
        );
    }

    /**
     * @Then the response status code should be :statusCode
     */
    public function theResponseStatusIs(int $statusCode)
    {
        $responseStatus = $this->response->getStatusCode();
        if ($responseStatus !== $statusCode) {
            throw new \Exception(
                sprintf('Given response status does not match actual one: %s', $responseStatus)
            );
        }
    }

    /**
     * @Then the response should contain:
     */
    public function theResponseShouldContain(PyStringNode $expectedResponse)
    {
        $rawExpectedResponse = $this->cleanUpJsonResponse($expectedResponse->getRaw());
        $response = $this->cleanUpJsonResponse($this->response->getBody()->getContents());

        if ($rawExpectedResponse !== $response) {
            throw new \Exception(
                sprintf('Given response does not match actual one: %s', print_r($response, true))
            );
        }
    }

    private function cleanUpJsonResponse(string $json): string
    {
        return json_encode(json_decode($json));
    }
}

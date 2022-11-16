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

namespace Adshares\AdsOperator\Controller;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Base class for REST API controllers.
 *
 * @package Adshares\AdsOperator\Controller
 */
class ApiController
{
    protected const INVALID_RESOURCE_MESSAGE = 'Invalid resource identity';

    /**
     * @var mixed
     */
    protected $repository;

    /**
     * @var string
     */
    protected $defaultSort = 'id';

    /**
     * @var string
     */
    protected $defaultOrder = 'desc';

    /**
     * @var int
     */
    protected $defaultLimit = 25;

    /**
     * @var int
     */
    protected $defaultOffset = 0;

    /**
     * @var int
     */
    protected $maxLimit = 100;

    /**
     * @var int
     */
    protected $maxOffset = PHP_INT_MAX;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string|null $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    protected function response(string $data = null, int $status = Response::HTTP_OK, array $headers = []): Response
    {
        $headers = array_merge($headers, ['content-type' => 'application/json']);

        return new Response($data, $status, $headers);
    }

    protected function validationErrorResponse(array $data, int $status = Response::HTTP_BAD_REQUEST): Response
    {
        if (!isset($data['message'])) {
            $data['message'] = 'Validation failed';
        }

        if (!isset($data['code'])) {
            $data['code'] = $status;
        }

        if (!isset($data['errors'])) {
            $data['errors'] = [];
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @param Request $request
     * @param array $availableSortingFields
     */
    protected function validateRequest(Request $request, array $availableSortingFields): void
    {
        $sort = $this->normalizeSort($request->get('sort'));
        $order = $request->get('order');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        if ($sort !== null && (!is_string($sort) || !in_array($sort, $availableSortingFields))) {
            throw new BadRequestHttpException(sprintf(
                'Sort value `%s` is invalid. Only %s values are supported.',
                $sort,
                implode(', ', $availableSortingFields)
            ));
        }

        if ($order !== null && (!is_string($order) || !in_array(strtolower($order), ['asc', 'desc']))) {
            throw new BadRequestHttpException(sprintf(
                'Order value `%s` is invalid. Only `desc` and `asc` values are supported.',
                $order
            ));
        }

        if ($offset !== null && (!is_numeric($offset) || ($offset < 0 || $offset > $this->maxOffset))) {
            throw new BadRequestHttpException(sprintf(
                'Offset value `%s` is invalid. Value must be between %s and %s.',
                $offset,
                0,
                $this->maxOffset
            ));
        }

        if ($limit !== null && (!is_numeric($limit) || ($limit < 1 || $limit > $this->maxLimit))) {
            throw new BadRequestHttpException(sprintf(
                'Limit value `%s` is invalid. Value must be between %s and %s.',
                $limit,
                1,
                $this->maxLimit
            ));
        }
    }

    /**
     * @param Request $request
     * @return int
     */
    protected function getLimit(Request $request): int
    {
        $limit = $request->get('limit');

        if ($limit === null) {
            $limit = $this->defaultLimit;
        }

        return (int)$limit;
    }

    /**
     * @param Request $request
     * @return int
     */
    protected function getOffset(Request $request): int
    {
        $offset = $request->get('offset');

        if ($offset === null) {
            $offset = $this->defaultOffset;
        }

        return (int)$offset;
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getSort(Request $request): string
    {
        $sort = $this->normalizeSort($request->get('sort'));

        if ($sort === null) {
            $sort = $this->defaultSort;
        }

        return $sort;
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getOrder(Request $request): string
    {
        $order = $request->get('order');

        if ($order === null) {
            $order = $this->defaultOrder;
        }

        return $order;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getList(Request $request): array
    {
        $this->validateRequest($request, $this->repository->availableSortingFields());

        $sort = $this->getSort($request);
        $order = $this->getOrder($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        return $this->repository->fetchList($sort, $order, $limit, $offset);
    }

    /**
     * @param null|string $snake
     * @return null|string
     */
    private function normalizeSort(?string $snake): ?string
    {
        if (!$snake) {
            return null;
        }

        return lcfirst(str_replace('_', '', ucwords($snake, '_')));
    }
}

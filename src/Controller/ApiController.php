<?php

namespace Adshares\AdsOperator\Controller;

use JMS\Serializer\SerializerInterface;
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

    /**
     * @param Request $request
     * @param array $availableSortingFields
     */
    protected function validateRequest(Request $request, array $availableSortingFields): void
    {
        $sort = $request->get('sort');
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
                'Offset value `%s` is invalid. Value must be between %s and %s',
                $offset,
                0,
                $this->maxOffset
            ));
        }

        if ($limit !== null && (!is_numeric($limit) || ($limit < 1 || $limit > $this->maxLimit))) {
            throw new BadRequestHttpException(sprintf(
                'Limit value `%s` is invalid. Value must be between %s and %s',
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

        return $limit;
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

        return $offset;
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getSort(Request $request): string
    {
        $sort = $request->get('sort');

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
}

<?php

namespace Adshares\AdsOperator\Controller;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiController
{
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
    protected $defaultLimit = 500;

    /**
     * @var int
     */
    protected $defaultOffset = 0;

    /**
     * @var int
     */
    protected $maxLimit = 1000;

    /**
     * @var int
     */
    protected $maxOffset = 100;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    protected function response($data = null, int $status = Response::HTTP_OK, array $headers = []): Response
    {
        $headers = array_merge($headers, ['content-type' => 'application/json']);

        return new Response($data, $status, $headers);
    }

    protected function validateRequest(Request $request, array $availableSortingFields): void
    {
        $sort = $request->get('sort');
        $order = $request->get('order');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        if ($order && !in_array(strtolower($order), ['asc', 'desc'])) {
            throw new BadRequestHttpException(sprintf(
                'Order value `%s` is invalid. Only `desc` and `asc` values are supported.',
                $order
            ));
        }

        if ($offset < 0 || $offset > $this->maxOffset) {
            throw new BadRequestHttpException(sprintf(
                'Offset value `%s` is invalid. Value must be between %s and %s',
                $offset,
                0,
                $this->maxOffset
            ));
        }

        if ($limit !== null && ($limit < 1 || $limit > $this->maxLimit)) {
            throw new BadRequestHttpException(sprintf(
                'Limit value `%s` is invalid. Value must be between %s and %s',
                $limit,
                1,
                $this->maxLimit
            ));
        }

        if ($sort && !in_array($sort, $availableSortingFields)) {
            throw new BadRequestHttpException(sprintf(
                'Sort value `%s` is invalid. Only %s values are supported.',
                $sort,
                implode(', ', $availableSortingFields)
            ));
        }
    }

    protected function getLimit(Request $request): int
    {
        $limit = $request->get('limit');

        if ($limit === null) {
            $limit = $this->defaultLimit;
        }

        return $limit;
    }

    protected function getOffset(Request $request): int
    {
        $offset = $request->get('offset');

        if ($offset === null) {
            $offset = $this->defaultOffset;
        }

        return $offset;
    }

    protected function getSort(Request $request): string
    {
        $sort = $request->get('sort');

        if ($sort === null) {
            $sort = $this->defaultSort;
        }

        return $sort;
    }

    protected function getOrder(Request $request): string
    {
        $order = $request->get('order');

        if ($order === null) {
            $order = $this->defaultOrder;
        }

        return $order;
    }
}

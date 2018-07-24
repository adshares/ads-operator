<?php

namespace Adshares\AdsOperator\Request;

use Symfony\Component\HttpFoundation\Request;

class Pagination
{
    const DEFAULT_SORT_FIELD = 'id';
    const DEFAULT_ORDER = 'desc';

    private $limit;
    private $offset;
    private $sort;
    private $order;

    public function __construct(Request $request, array $availableSortingFields = [])
    {
        $sort = $request->get('sort');
        $order = $request->get('order');
        $limit = (int) $request->get('limit');
        $offset = (int) $request->get('offset');

        if ($offset < 0) {
            $offset = 0;
        }

        if ($limit < 0) {
            $limit = 0;
        }

        if (!in_array($sort, $availableSortingFields)) {
            $sort = self::DEFAULT_SORT_FIELD;
        }

        if ($order !== 'asc' && $order !== 'desc') {
            $order = self::DEFAULT_ORDER;
        }

        $this->limit = $limit;
        $this->offset = $offset;
        $this->sort = $sort;
        $this->order = $order;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}

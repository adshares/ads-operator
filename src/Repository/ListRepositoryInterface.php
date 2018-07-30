<?php

namespace Adshares\AdsOperator\Repository;

/**
 * ListRepositoryInterface provides methods for listing data from database.
 *
 * @package Adshares\AdsOperator\Repository
 */
interface ListRepositoryInterface
{
    /**
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchList(string $sort, string $order, int $limit, int $offset): array;

    /**
     * @return array
     */
    public function availableSortingFields(): array;
}

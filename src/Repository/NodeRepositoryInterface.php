<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Node;

/**
 * Interface NodeRepositoryInterface
 * @package Adshares\AdsOperator\Repository
 */
interface NodeRepositoryInterface
{
    /**
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findNodes(string $sort, string $order, int $limit, int $offset): array;

    /**
     * @param string $nodeId
     * @return Node
     */
    public function getNode(string $nodeId): Node;

    /**
     * @return array
     */
    public function availableSortingFields(): array;
}

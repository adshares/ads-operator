<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Node;

interface NodeRepositoryInterface
{
    public function findNodes(string $sort, string $order, int $limit, int $offset): array;

    public function getNode(string $nodeId): Node;

    public function availableSortingFields(): array;
}

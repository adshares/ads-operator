<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Node;

interface NodeRepositoryInterface
{
    public function findNodes(string $sort, string $order, int $limit = 100, int $offset = 0): array;

    public function getNode(string $nodeId): Node;

    public function availableSortingFields(): array;
}

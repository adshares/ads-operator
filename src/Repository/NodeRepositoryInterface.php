<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Request\Pagination;
use Adshares\AdsOperator\Document\Node;

interface NodeRepositoryInterface
{
    public function findNodes(Pagination $pagination): array;

    public function getNode(string $nodeId): Node;

    public function availableSortingFields(): array;
}

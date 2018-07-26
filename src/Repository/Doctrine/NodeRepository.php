<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Repository\NodeRepositoryInterface;

/**
 * Class NodeRepository
 * @package Adshares\AdsOperator\Repository\Doctrine
 */
class NodeRepository extends BaseRepository implements NodeRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'balance',
        ];
    }

    /**
     * @param string $nodeId
     * @return Node
     */
    public function getNode(string $nodeId): Node
    {
        /** @var Node $node */
        $node = $this->findOneBy(['id' => $nodeId]);

        return $node;
    }
}

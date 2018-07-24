<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Repository\NodeRepositoryInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

class NodeRepository extends DocumentRepository implements NodeRepositoryInterface
{
    public function availableSortingFields(): array
    {
        return [
            'id',
        ];
    }

    public function findNodes(string $sort, string $order, int $limit = 100, int $offset = 0): array
    {
        try {
            $nodes = $this
                ->createQueryBuilder()
                ->sort($sort, $order)
                ->limit($limit)
                ->skip($offset)
                ->getQuery()
                ->execute();
        } catch (MongoDBException $ex) {
            return [];
        }

        return $nodes->toArray();
    }

    public function getNode(string $nodeId): Node
    {
        /** @var Node $node */
        $node = $this->findOneBy(['id' => $nodeId]);

        return $node;
    }
}

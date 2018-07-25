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
            'balance',
        ];
    }

    public function findNodes(string $sort, string $order, int $limit, int $offset): array
    {
        $nodes = [];

        try {
            $cursor = $this
                ->createQueryBuilder()
                ->sort($sort, $order)
                ->limit($limit)
                ->skip($offset)
                ->getQuery()
                ->execute();

            $data = $cursor->toArray();

            foreach ($data as $id => $node) {
                $nodes[] = $node;
            }
        } catch (MongoDBException $ex) {
            return [];
        }

        return $nodes;
    }

    public function getNode(string $nodeId): Node
    {
        /** @var Node $node */
        $node = $this->findOneBy(['id' => $nodeId]);

        return $node;
    }
}

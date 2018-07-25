<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Repository\NodeRepositoryInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

/**
 * Class NodeRepository
 * @package Adshares\AdsOperator\Repository\Doctrine
 */
class NodeRepository extends DocumentRepository implements NodeRepositoryInterface
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
     * {@inheritdoc}
     */
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

            foreach ($data as $node) {
                $nodes[] = $node;
            }
        } catch (MongoDBException $ex) {
            return [];
        }

        return $nodes;
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

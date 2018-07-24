<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Repository\NodeRepositoryInterface;
use Adshares\AdsOperator\Request\Pagination;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

class NodeRepository extends DocumentRepository implements NodeRepositoryInterface
{
    private $fields = [
        '_id',
        'id',
        'accountCount',
        'balance',
        'ip',
        'packCount',
        'port',
        'publickKey',
    ];

    public function availableSortingFields(): array
    {
        return [
            'id',
        ];
    }

    public function findNodes(Pagination $pagination):? array
    {
        try {
            $nodes = $this
                ->createQueryBuilder()
                ->hydrate(false)
                ->select($this->fields)
                ->sort($pagination->getSort(), $pagination->getOrder())
                ->limit($pagination->getLimit())
                ->skip($pagination->getOffset())
                ->getQuery()
                ->execute();
        } catch (MongoDBException $ex) {
            return [];
        }

        if ($nodes) {
            $results = [];
            foreach ($nodes as $item) {
                $results[] = $item;
            }

            return $results;
        }

        return [];
    }

    public function getNode(string $nodeId): Node
    {
        /** @var Node $node */
        $node = $this->findOneBy(['id' => $nodeId]);

        return $node;
    }
}

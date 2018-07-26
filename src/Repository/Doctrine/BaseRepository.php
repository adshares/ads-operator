<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

/**
 * BaseRepository provides common methods used in all repositories.
 * @package Adshares\AdsOperator\Repository\Doctrine
 */
class BaseRepository extends DocumentRepository
{
    /**
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchList(string $sort, string $order, int $limit, int $offset): array
    {
        $results = [];

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
                $results[] = $node;
            }
        } catch (MongoDBException $ex) {
            return [];
        }

        return $results;
    }
}

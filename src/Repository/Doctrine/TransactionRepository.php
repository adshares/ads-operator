<?php
/**
 * Copyright (C) 2018 Adshares sp. z. o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator.  If not, see <https://www.gnu.org/licenses/>
 */

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\Ads\Entity\Transaction\AbstractTransaction;
use Adshares\AdsOperator\Repository\TransactionRepositoryInterface;
use Doctrine\ODM\MongoDB\MongoDBException;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'blockId',
            'type',
        ];
    }

    /**
     * @param string $transactionId
     * @return AbstractTransaction|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getTransaction(string $transactionId):? AbstractTransaction
    {
        /** @var AbstractTransaction $transaction */
        $transaction = $this->find($transactionId);

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionsByAccountId(
        string $accountId,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        $results = [];

        try {
            $queryBuilder = $this->createQueryBuilder();

            $cursor = $queryBuilder
                ->addOr($queryBuilder->expr()->field('senderAddress')->equals($accountId))
                ->addOr($queryBuilder->expr()->field('targetAddress')->equals($accountId))
                ->addOr($queryBuilder->expr()->field('wires.targetAddress')->equals($accountId))
                ->sort($sort, $order)
                ->limit($limit)
                ->skip($offset)
                ->getQuery()
                ->execute();

            $data = $cursor->toArray();

            foreach ($data as $transaction) {
                $results[] = $transaction;
            }

            return $results;
        } catch (MongoDBException $ex) {
            return [];
        }
    }

    /**
     * @param string $messageId
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws MongoDBException
     */
    public function getTransactionsByMessageId(
        string $messageId,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        $results = [];

        $cursor = $this
            ->createQueryBuilder()
            ->field('messageId')->equals($messageId)
            ->sort($sort, $order)
            ->limit($limit)
            ->skip($offset)
            ->getQuery()
            ->execute();

        $data = $cursor->toArray();

        foreach ($data as $message) {
            $results[] = $message;
        }

        return $results;
    }
}

<?php

/**
 * Copyright (c) 2018-2022 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types=1);

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\Ads\Entity\Transaction\AbstractTransaction;
use Adshares\AdsOperator\Repository\TickerRepositoryInterface;
use Adshares\AdsOperator\Repository\TransactionRepositoryInterface;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Builder;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface, TickerRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'nodeId',
            'blockId',
            'messageId',
            'type',
            'size',
            'amount',
            'senderAddress',
            'targetAddress',
            'time',
        ];
    }

    /**
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param array $conditions
     * @return array
     */
    public function fetchList(
        string $sort,
        string $order,
        int $limit,
        int $offset,
        array $conditions = []
    ): array {
        return $this->getTransactions($conditions, true, $sort, $order, $limit, $offset);
    }

    /**
     * @param string $transactionId
     * @return AbstractTransaction|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getTransaction(string $transactionId): ?AbstractTransaction
    {
        /** @var AbstractTransaction $transaction */
        $transaction = $this->find($transactionId);

        return $transaction;
    }

    /**
     * @param array $conditions
     * @param bool $hideConnections
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getTransactions(
        array $conditions,
        bool $hideConnections,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        $results = [];
        $count = 0;

        $cursor = $this->createBuilderForList($sort, $order, $limit, $offset, $conditions);
        if ($hideConnections) {
            $cursor->field('type')->notEqual('connection');
        }

        try {
            $cursor = $cursor
                ->getQuery()
                ->execute();

            $data = $cursor->toArray();
            $count = $cursor->count();

            foreach ($data as $transaction) {
                $results[] = $transaction;
            }
        } catch (MongoDBException $ex) {
        }

        return [
            'data' => $results,
            'meta' => ['count' => $count]
        ];
    }

    /**
     * @param string $accountId
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getTransactionsByAccountId(
        string $accountId,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        $results = [];
        $count = 0;

        try {
            $queryBuilder = $this->createQueryBuilder();

            $cursor = $queryBuilder
                ->field('type')->notEqual('connection')
                ->addOr($queryBuilder->expr()->field('senderAddress')->equals($accountId))
                ->addOr($queryBuilder->expr()->field('targetAddress')->equals($accountId))
                ->addOr($queryBuilder->expr()->field('wires.targetAddress')->equals($accountId))
                ->sort($sort, $order)
                ->limit($limit)
                ->skip($offset)
                ->getQuery()
                ->execute();

            $data = $cursor->toArray();
            $count = $cursor->count();

            foreach ($data as $transaction) {
                $results[] = $transaction;
            }
        } catch (MongoDBException $ex) {
        }

        return [
            'data' => $results,
            'meta' => ['count' => $count]
        ];
    }

    /**
     * @param string $messageId
     * @param bool $hideConnections
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getTransactionsByMessageId(
        string $messageId,
        bool $hideConnections,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        return $this->getTransactions(['messageId' => $messageId], $hideConnections, $sort, $order, $limit, $offset);
    }

    /**
     * @param string $nodeId
     * @param bool $hideConnections
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getTransactionsByNodeId(
        string $nodeId,
        bool $hideConnections,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        return $this->getTransactions(['nodeId' => $nodeId], $hideConnections, $sort, $order, $limit, $offset);
    }

    /**
     * @param string $blockId
     * @param bool $hideConnections
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getTransactionsByBlockId(
        string $blockId,
        bool $hideConnections,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        return $this->getTransactions(['blockId' => $blockId], $hideConnections, $sort, $order, $limit, $offset);
    }

    /**
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param array|null $conditions
     * @return Builder
     */
    public function createBuilderForList(
        string $sort,
        string $order,
        int $limit,
        int $offset,
        ?array $conditions = []
    ): Builder {
        $cursor = $this
            ->createQueryBuilder()
            ->sort($sort, $order)
            ->limit($limit)
            ->skip($offset);

        if ($conditions) {
            foreach ($conditions as $columnName => $value) {
                $cursor->field($columnName)->equals($value);
            }
        }

        return $cursor;
    }

    /**
     * @return array
     */
    public function availableTickerIntervals(): array
    {
        return [
            'year',
            'month',
            'day',
            'hour',
            'minute',
            'second',
        ];
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @param string $interval
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getTickers(
        \DateTime $start,
        \DateTime $end,
        string $interval,
        int $limit,
        int $offset
    ): array {

        $results = [];
        try {
            $builder = $this->createAggregationBuilder();

            $builder->hydrate('Adshares\AdsOperator\Document\Stats\TransactionTicker');
            $builder->addFields()
                ->field('manyAmount')
                ->expression($builder->expr()->sum(
                    $builder->expr()->map('$wires', 'el', '$$el.amount')
                ));
            $builder->match()
                ->field('time')
                ->gte($start)
                ->lt($end);
            $builder->group()
                ->field('_id')
                ->dateToString(self::getTimeFormat($interval), '$time')
                ->field('quantity')
                ->sum(1)
                ->field('fee')
                ->sum('$senderFee')
                ->field('oneVolume')
                ->sum('$amount')
                ->field('manyVolume')
                ->sum('$manyAmount');
            $builder->project()
                ->field('date')
                ->expression('$_id')
                ->field('quantity')
                ->expression('$quantity')
                ->field('fee')
                ->divide('$fee', 100000000000)
                ->field('volume')
                ->divide($builder->expr()->sum('$oneVolume', '$manyVolume'), 100000000000);

            $cursor = $builder
                ->sort('date', 'asc')
                ->skip($offset)
                ->limit($limit)
                ->execute();

            $data = $cursor->toArray();

            foreach ($data as $ticker) {
                $results[] = $ticker;
            }
        } catch (MongoDBException $ex) {
        }

        return $results;
    }

    private static function getTimeFormat(string $interval): string
    {
        switch ($interval) {
            case 'year':
                return '%Y-01-01';
            case 'month':
                return '%Y-%m-01';
            case 'hour':
                return '%Y-%m-%dT%H:00:00';
            case 'minute':
                return '%Y-%m-%dT%H:%M:00';
            case 'second':
                return '%Y-%m-%dT%H:%M:%S';
            default:
                return '%Y-%m-%d';
        }
    }
}

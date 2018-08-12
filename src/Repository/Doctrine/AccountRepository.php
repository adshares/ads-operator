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

use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Repository\AccountRepositoryInterface;
use Doctrine\ODM\MongoDB\MongoDBException;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'time',
        ];
    }

    /**
     * @param string $accountId
     * @return Account|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getAccount(string $accountId):? Account
    {
        /** @var Account $account */
        $account = $this->find($accountId);

        return $account;
    }

    public function getAccountsByNodeId(string $nodeId): array
    {
        $results = [];

        try {
            $cursor = $this
                ->createQueryBuilder()
                ->field('nodeId')->equals($nodeId)
                ->getQuery()
                ->execute();

            $data = $cursor->toArray();

            foreach ($data as $node) {
                $results[] = $node;
            }

            return $results;
        } catch (MongoDBException $ex) {
            return [];
        }
    }
}

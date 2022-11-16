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

use Adshares\AdsOperator\Document\Snapshot;
use Adshares\AdsOperator\Document\SnapshotAccount;
use Adshares\AdsOperator\Repository\SnapshotAccountRepositoryInterface;
use Adshares\AdsOperator\Repository\SnapshotRepositoryInterface;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;

class SnapshotAccountRepository extends BaseRepository implements SnapshotAccountRepositoryInterface
{
    public function availableSortingFields(): array
    {
        return [
            'id',
            'accountId',
            'nodeId',
            'messageCount',
            'transactionCount',
            'balance',
            'time',
            'localChange',
            'remoteChange',
        ];
    }

    public function getAccount(string $snapshotId, string $accountId): ?SnapshotAccount
    {
        return $this->find(sprintf('%s/%s', $snapshotId, $accountId));
    }

    public function fetchListBySnapshotId(
        string $snapshotId,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        return $this->fetchList($sort, $order, $limit, $offset, ['snapshotId' => $snapshotId]);
    }
}

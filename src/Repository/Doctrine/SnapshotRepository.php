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

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Document\Snapshot;
use Adshares\AdsOperator\Repository\SnapshotRepositoryInterface;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;

class SnapshotRepository extends BaseRepository implements SnapshotRepositoryInterface
{
    public function availableSortingFields(): array
    {
        return [
            'id',
            'time',
        ];
    }

    /**
     * @throws MappingException
     * @throws LockException
     */
    public function getSnapshot(string $snapshotId): ?Snapshot
    {
        return $this->find($snapshotId);
    }
}

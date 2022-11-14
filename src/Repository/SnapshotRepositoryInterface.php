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

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Snapshot;

/**
 * Interface BlockRepositoryInterface
 * @package Adshares\AdsOperator\Repository
 */
interface SnapshotRepositoryInterface extends ListRepositoryInterface
{
    /**
     * @param string $snapshotId
     * @return Snapshot
     */
    public function getSnapshot(string $snapshotId): ?Snapshot;

    /**
     * @param string $snapshotId
     * @return array
     */
    public function getSnapshotNodes(string $snapshotId): array;

    /**
     * @param string $blockId
     * @param string $nodeId
     * @return array
     */
    public function getSnapshotAccounts(string $snapshotId, string $nodeId): array;

    /**
     * @param string $blockId
     * @return SnapshotAccount
     */
    public function getSnapshotAccount(string $snapshotId, string $accountId): SnapshotAccount;
}

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

namespace Adshares\AdsOperator\Document;

use ReflectionClass;

class SnapshotAccount extends Account
{
    protected string $snapshotId;

    protected string $accountId;

    public function setSnapshotId(string $snapshotId): self
    {
        $this->snapshotId = $snapshotId;
        $this->id = sprintf('%s/%s', $snapshotId, $this->accountId);
        return $this;
    }

    public function getSnapshotId(): string
    {
        return $this->snapshotId;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getNodeId(): string
    {
        return $this->nodeId;
    }

    public static function create(?string $address = null): self
    {
        $x = new self();
        if (null !== $address) {
            $x->address = $address;
            $x->accountId = $address;
        }
        return $x;
    }

    public function fillWithRawData(array $data): void
    {
        parent::fillWithRawData($data);
        $this->accountId = $data['_id'];
    }

    protected static function castProperty(string $name, $value, ReflectionClass $refClass = null)
    {
        if ('balance' === $name) {
            return $value;
        }
        if ('time' === $name || 'localChange' === $name || 'remoteChange' === $name) {
            return $value->toDateTime();
        }
        return parent::castProperty($name, $value, $refClass);
    }
}

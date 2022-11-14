<?php

/**
 * Copyright (C) 2018 Adshares sp. z o.o.
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

declare(strict_types=1);

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Repository\BlockRepositoryInterface;
use Adshares\AdsOperator\Document\Block;

/**
 * Class BlockRepository
 * @package Adshares\AdsOperator\Repository\Doctrine
 */
class BlockRepository extends BaseRepository implements BlockRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'time',
            'endTime',
            'messageCount',
            'nodeCount',
            'transactionCount',
        ];
    }

    /**
     * @param string $blockId
     * @return Block|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getBlock(string $blockId): ?Block
    {
        /** @var Block $block */
        $block = $this->find($blockId);

        return $block;
    }
}

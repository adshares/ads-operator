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

namespace Adshares\AdsOperator\Document;

/**
 * Class Block
 * @package Adshares\AdsOperator\Document
 */
class Block extends \Adshares\Ads\Entity\Block
{
    /**
     * @var int
     */
    protected $transactionCount = 0;

    /**
     * Block constructor.
     * @param string|null $id
     * @param array $nodes
     * @param int|null $messageCount
     */
    public function __construct(string $id = null, array $nodes = [], int $messageCount = null)
    {
        if (null !== $id) {
            $this->id = $id;
        }

        if ($nodes) {
            $this->nodes = $nodes;
        }

        if (null !== $messageCount) {
            $this->messageCount = $messageCount;
        }
    }

    /**
     * @param int $count
     */
    public function setTransactionCount(int $count): void
    {
        $this->transactionCount = $count;
    }

    /**
     * @return int
     */
    public function getTransactionCount(): int
    {
        return $this->transactionCount;
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function validateId(string $id): bool
    {
        return (bool) preg_match('/^[0-9A-F]{8}$/', $id);
    }
}

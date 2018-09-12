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

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Message;

/**
 * Interface MessageRepositoryInterface
 * @package Adshares\AdsOperator\Repository
 */
interface MessageRepositoryInterface extends ListRepositoryInterface
{
    /**
     * @param string $messageId
     * @return Message
     */
    public function getMessage(string $messageId):? Message;

    /**
     * @param string $blockId
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getMessagesByBlockId(
        string $blockId,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array;

    /**
     * @param string $nodeId
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getMessagesByNodeId(
        string $nodeId,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array;
}

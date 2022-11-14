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

use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Repository\MessageRepositoryInterface;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'blockId',
            'nodeId',
            'transactionCount',
            'length',
            'time'
        ];
    }

    /**
     * @param string $messageId
     * @return Message|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getMessage(string $messageId): ?Message
    {
        /** @var Message $message */
        $message = $this->find($messageId);

        return $message;
    }

    /**
     * @param string $blockId
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getMessagesByBlockId(
        string $blockId,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        return $this->fetchList($sort, $order, $limit, $offset, ['blockId' => $blockId]);
    }

    /**
     * @param string $nodeId
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function getMessagesByNodeId(
        string $nodeId,
        string $sort,
        string $order,
        int $limit,
        int $offset
    ): array {
        return $this->fetchList($sort, $order, $limit, $offset, ['nodeId' => $nodeId]);
    }
}

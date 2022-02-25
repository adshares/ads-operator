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

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\NetworkTransaction as BaseNetworkTransaction;

/**
 * Class NetworkTransaction
 * @package Adshares\AdsOperator\Document\Transaction
 */
class NetworkTransaction extends BaseNetworkTransaction implements ArrayableInterface
{
    /**
     * @var string
     */
    protected $senderAddress;

    /**
     * @var string
     */
    protected $targetAddress;


    public function getSenderFee(): int
    {
        switch ($this->type) {
            case 'create_account':
                return 100000000 + 20000000;
            case 'account_created':
                return 100000000;
            case 'retrieve_funds':
                return 2 * 1000000;
            case 'set_account_status':
                return 10000000;
            case 'unset_account_status':
                return 10000000;
            case 'set_bank_status':
                return 10000000;
            case 'unset_bank_status':
                return 10000000;
        }
        throw new \RuntimeException("Unknown transaction fee for '{$this->type}");
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            '_id' => $this->id,
            'size' => $this->size,
            'type' => $this->type,
            'nodeId' => $this->nodeId,
            'blockId' => $this->blockId,
            'messageId' => $this->messageId,
            'msgId' => $this->msgId,
            'node' => $this->node,
            'signature' => $this->signature,
            'targetNode' => $this->targetNode,
            'targetUser' => $this->targetUser,
            'time' => $this->time,
            'user' => $this->user,
            'senderAddress' => $this->getSenderAddress(),
            'targetAddress' => $this->getTargetAddress(),
        ];
    }
}

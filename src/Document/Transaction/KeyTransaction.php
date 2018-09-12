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
use Adshares\Ads\Entity\Transaction\KeyTransaction as BaseKeyTransaction;

/**
 * Class KeyTransaction
 * @package Adshares\AdsOperator\Document\Transaction
 */
class KeyTransaction extends BaseKeyTransaction implements ArrayableInterface
{
    /**
     * @var string
     */
    protected $senderAddress;

    /**
     * @var string
     */
    protected $targetAddress;

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
            'newPublicKey' => $this->newPublicKey,
            'publicKey' => $this->publicKey,
            'oldPublicKey' => $this->oldPublicKey,
            'publicKeySignature' => $this->publicKeySignature,
            'signature' => $this->signature,
            'node' => $this->node,
            'targetNode' => $this->targetNode,
            'targetUser' => $this->targetUser,
            'time' => $this->time,
            'user' => $this->user,
            'senderAddress' => $this->getSenderAddress(),
            'targetAddress' => $this->getTargetAddress(),
        ];
    }
}

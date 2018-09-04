<?php
/**
 * Copyright (C) 2018 Adshares sp. z. o.o.
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

use Adshares\Ads\Entity\Transaction\SendManyTransaction as BaseSendManyTransaction;
use Adshares\Ads\Entity\Transaction\SendManyTransactionWire;
use Adshares\AdsOperator\Document\ArrayableInterface;

/**
 * Class SendManyTransaction
 * @package Adshares\AdsOperator\Document\Transaction
 */
class SendManyTransaction extends BaseSendManyTransaction implements ArrayableInterface
{
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
            'senderAddress' => $this->senderAddress,
            'senderFee' => $this->senderFee,
            'signature' => $this->signature,
            'wireCount' => $this->wireCount,
            'time' => $this->time,
            'user' => $this->user,
            'wires' => $this->transformTransactionWiresToArray($this->wires),
        ];
    }

    /**
     * @param array $wires
     * @return array
     */
    private function transformTransactionWiresToArray(array $wires): array
    {
        $transformed = [];

        /** @var SendManyTransactionWire $transaction */
        foreach ($wires as $transaction) {
            $transformed[] = [
                'amount' => $transaction->getAmount(),
                'targetAddress' => $transaction->getTargetAddress(),
                'targetNode' => $transaction->getTargetNode(),
                'targetUser' => $transaction->getTargetUser(),
            ];
        }

        return $transformed;
    }
}

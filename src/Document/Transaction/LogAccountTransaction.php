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

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\LogAccountTransaction as BaseLogAccountTransaction;

/**
 * Class LogAccountTransaction
 * @package Adshares\AdsOperator\Document\Transaction
 */
class LogAccountTransaction extends BaseLogAccountTransaction implements ArrayableInterface
{
    /**
     * @var string
     */
    protected $senderAddress;

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
            'networkAccount' => $this->transformNetworkAccountToArray(),
            'node' => $this->node,
            'signature' => $this->signature,
            'time' => $this->time,
            'user' => $this->user,
            'senderAddress' => $this->getSenderAddress(),
        ];
    }

    /**
     * @return array
     */
    private function transformNetworkAccountToArray(): array
    {
        return [
            'address' => $this->networkAccount->getAddress(),
            'balance' => $this->networkAccount->getBalance(),
            'hash' => $this->networkAccount->getHash(),
            'localChange' => $this->networkAccount->getLocalChange(),
            'msid' => $this->networkAccount->getMsid(),
            'node' => $this->networkAccount->getNode(),
            'pairedAddress' => $this->networkAccount->getPairedAddress(),
            'pairedNode' => $this->networkAccount->getPairedNode(),
            'publicKey' => $this->networkAccount->getPublicKey(),
            'remoteChange' => $this->networkAccount->getRemoteChange(),
            'status' => $this->networkAccount->getStatus(),
            'time' => $this->networkAccount->getTime(),

        ];
    }
}

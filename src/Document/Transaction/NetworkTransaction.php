<?php

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
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            '_id' => $this->id,
            'size' => $this->size,
            'type' => $this->type,
            'blockId' => $this->blockId,
            'messageId' => $this->messageId,
            'msgId' => $this->msgId,
            'node' => $this->node,
            'signature' => $this->signature,
            'targetNode' => $this->targetNode,
            'targetUser' => $this->targetUser,
            'time' => $this->time,
            'user' => $this->user,
        ];
    }
}

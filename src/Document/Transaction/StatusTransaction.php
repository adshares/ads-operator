<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\Ads\Entity\Transaction\StatusTransaction as BaseStatusTransaction;
use Adshares\AdsOperator\Document\ArrayableInterface;

/**
 * Class StatusTransaction
 * @package Adshares\AdsOperator\Document\Transaction
 */
class StatusTransaction extends BaseStatusTransaction implements ArrayableInterface
{
    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'size' => $this->size,
            'type' => $this->type,
            'blockId' => $this->blockId,
            'messageId' => $this->messageId,
            'msgId' => $this->msgId,
            'signature' => $this->signature,
            'node' => $this->node,
            'targetNode' => $this->targetNode,
            'targetUser' => $this->targetUser,
            'time' => $this->time,
            'user' => $this->user,
        ];
    }
}

<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\SendOneTransaction as BaseSendOneTransaction;

/**
 * Class SendOneTransaction
 * @package Adshares\AdsOperator\Document\Transaction
 */
class SendOneTransaction extends BaseSendOneTransaction implements ArrayableInterface
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
            'amount' => $this->amount,
            'message' => $this->message,
            'msgId' => $this->msgId,
            'node' => $this->node,
            'senderAddress' => $this->senderAddress,
            'senderFee' => $this->senderFee,
            'signature' => $this->signature,
            'targetAddress' => $this->targetAddress,
            'targetNode' => $this->targetNode,
            'targetUser' => $this->targetUser,
            'time' => $this->time,
            'user' => $this->user,
        ];
    }
}

<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\BroadcastTransaction as BaseBroadcastTransaction;

class BroadcastTransaction extends BaseBroadcastTransaction implements ArrayableInterface
{
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'size' => $this->size,
            'type' => $this->type,
            'blockId' => $this->blockId,
            'messageId' => $this->messageId,
            'message' => $this->message,
            'messageLength' => $this->messageLength,
            'msgId' => $this->msgId,
            'node' => $this->node,
            'signature' => $this->signature,
            'time' => $this->time,
            'user' => $this->user,
        ];
    }
}

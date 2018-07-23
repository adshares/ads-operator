<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\NetworkTransaction as BaseNetworkTransaction;

class NetworkTransaction extends BaseNetworkTransaction implements ArrayableInterface
{
    public function toArray(): array
    {
        return [
            'id' => $this->id,
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

<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\KeyTransaction as BaseKeyTransaction;

class KeyTransaction extends BaseKeyTransaction implements ArrayableInterface
{
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "size" => $this->size,
            "type" => $this->type,
            "blockId" => $this->blockId,
            "messageId" => $this->messageId,
            "msgId" => $this->msgId,
            "newPublicKey" => $this->newPublicKey,
            "oldPublicKey" => $this->oldPublicKey,
            "publicKeySignature" => $this->publicKeySignature,
            "signature" => $this->signature,
            "node" => $this->node,
            "targetNode" => $this->targetNode,
            "targetUser" => $this->targetUser,
            "time" => $this->time,
            "user" => $this->user,
        ];
    }
}

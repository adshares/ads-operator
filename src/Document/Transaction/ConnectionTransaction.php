<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\ConnectionTransaction as BaseConnectionTransaction;

class ConnectionTransaction extends BaseConnectionTransaction implements ArrayableInterface
{
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "size" => $this->size,
            "type" => $this->type,
            "blockId" => $this->blockId,
            "messageId" => $this->messageId,
            "ipAddress" => $this->ipAddress,
            "port" => $this->port,
        ];
    }
}

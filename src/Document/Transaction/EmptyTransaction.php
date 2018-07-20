<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\EmptyTransaction as BaseEmptyTransaction;

class EmptyTransaction extends BaseEmptyTransaction implements ArrayableInterface
{
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "size" => $this->size,
            "type" => $this->type,
            "blockId" => $this->blockId,
            "messageId" => $this->messageId,
        ];
    }
}

<?php

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\EmptyTransaction as BaseEmptyTransaction;

/**
 * Class EmptyTransaction
 * @package Adshares\AdsOperator\Document\Transaction
 */
class EmptyTransaction extends BaseEmptyTransaction implements ArrayableInterface
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
        ];
    }
}

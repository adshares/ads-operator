<?php

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
            'networkAccount' => (array) $this->networkAccount,
            'node' => $this->node,
            'signature' => $this->signature,
            'time' => $this->time,
            'user' => $this->user,
        ];
    }
}

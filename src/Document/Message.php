<?php

namespace Adshares\AdsOperator\Document;

class Message extends \Adshares\Ads\Entity\Message
{
    /**
     * @var int
     */
    protected $transactionCount;

    public function __construct(?int $transactionCount = 0)
    {
        if ($transactionCount) {
            $this->transactionCount = $transactionCount;
        }
    }

    public function setTransactionCount(int $count): void
    {
        $this->transactionCount = $count;
    }

    public function getTransactionCount(): int
    {
        return $this->transactionCount;
    }
}

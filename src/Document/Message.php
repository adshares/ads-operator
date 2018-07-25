<?php

namespace Adshares\AdsOperator\Document;

/**
 * Class Message
 * @package Adshares\AdsOperator\Document
 */
class Message extends \Adshares\Ads\Entity\Message
{
    /**
     * @var int
     */
    protected $transactionCount;

    /**
     * Message constructor.
     * @param int|null $transactionCount
     */
    public function __construct(?int $transactionCount = 0)
    {
        if ($transactionCount) {
            $this->transactionCount = $transactionCount;
        }
    }

    /**
     * @param int $count
     */
    public function setTransactionCount(int $count): void
    {
        $this->transactionCount = $count;
    }

    /**
     * @return int
     */
    public function getTransactionCount(): int
    {
        return $this->transactionCount;
    }
}

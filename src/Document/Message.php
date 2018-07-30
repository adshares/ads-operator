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
     * @var string
     */
    protected $nodeId;

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

    /**
     * @param string $id
     * @return bool
     */
    public static function validateId(string $id): bool
    {
        return (bool) preg_match('/^[0-9A-Z]{4}:[0-9A-Z]{8}$/', $id);
    }
}

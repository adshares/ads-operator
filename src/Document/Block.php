<?php


namespace Adshares\AdsOperator\Document;

/**
 * Class Block
 * @package Adshares\AdsOperator\Document
 */
class Block extends \Adshares\Ads\Entity\Block
{
    /**
     * @var int
     */
    protected $transactionCount = 0;

    /**
     * Block constructor.
     * @param string|null $id
     * @param array $nodes
     * @param int|null $messageCount
     */
    public function __construct(string $id = null, array $nodes = [], int $messageCount = null)
    {
        if (null !== $id) {
            $this->id = $id;
        }

        if ($nodes) {
            $this->nodes = $nodes;
        }

        if (null !== $messageCount) {
            $this->messageCount = $messageCount;
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
        return (bool) preg_match('/^[0-9A-Z]{8}$/', $id);
    }
}

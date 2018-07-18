<?php


namespace Adshares\AdsOperator\Document;

class Block extends \Adshares\Ads\Entity\Block
{
    protected $transactionCount = 0;

    public function __construct($id = null, array $nodes = [], int $messageCount = null)
    {
        if ($id) {
            $this->id = $id;
        }

        if ($nodes) {
            $this->nodes = $nodes;
        }

        if ($messageCount) {
            $this->messageCount = $messageCount;
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

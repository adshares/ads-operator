<?php


namespace Adshares\AdsManager\Document;

class Block extends \Adshares\Ads\Entity\Block
{
    protected $transactionCount = 0;

    public function __construct($id = null, array $nodes = [])
    {
        if ($id) {
            $this->id = $id;
        }

        if ($nodes) {
            $this->nodes = $nodes;
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

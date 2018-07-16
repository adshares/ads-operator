<?php


namespace Adshares\AdsManager\Document;

class Package extends \Adshares\Ads\Entity\Package
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var int
     */
    protected $transactionCount = 0;

    public function __construct(string $id = null, int $node = null, int $nodeMsid = null)
    {
        if ($id) {
            $this->id = $id;
        }

        if ($node) {
            $this->node = (string) $node;
        }

        if ($nodeMsid) {
            $this->nodeMsid = $nodeMsid;
        }
    }

    public function generateId(): void
    {
        $this->id = str_pad(
            dechex(((int)$this->getNode() << 32) + $this->getNodeMsid()),
            12,
            '0',
            STR_PAD_LEFT
        );
    }

    public function setTransactionCount(int $count): void
    {
        $this->transactionCount = $count;
    }

    public function getTransactionCount(): int
    {
        return $this->transactionCount;
    }

    public function getId(): string
    {
        return $this->id;
    }
}

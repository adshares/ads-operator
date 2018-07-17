<?php


namespace Adshares\AdsOperator\Document;

class Transaction extends \Adshares\Ads\Entity\Transaction
{
    /**
     * @var  string
     */
    protected $blockId;

    /**
     * @var string
     */
    protected $packageId;

    public function setBlockId(string $blockId): void
    {
        $this->blockId = $blockId;
    }

    public function setPackageId(string $packageId): void
    {
        $this->packageId = $packageId;
    }

    public function getBlockId(): string
    {
        return $this->blockId;
    }

    public function getPackageId(): ?string
    {
        return $this->packageId;
    }
}

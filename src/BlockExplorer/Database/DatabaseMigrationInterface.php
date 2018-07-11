<?php


namespace Adshares\AdsManager\BlockExplorer\Database;

use Adshares\Ads\Entity\Block;
use Adshares\Ads\Entity\Package;

interface DatabaseMigrationInterface
{
    /**
     * @param Package $package
     * @param Block $block
     */
    public function addPackageToDatabase(Package $package, Block $block): void;

    /**
     * @param Block $block
     */
    public function addBlockToDatabase(Block $block): void;

    /**
     * @return int|null
     */
    public function getNewestBlockTime():? int;
}

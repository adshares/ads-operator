<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Block;

/**
 * Interface BlockRepositoryInterface
 * @package Adshares\AdsOperator\Repository
 */
interface BlockRepositoryInterface extends ListRepositoryInterface
{
    /**
     * @param string $blockId
     * @return Block
     */
    public function getBlock(string $blockId):? Block;
}

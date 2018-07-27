<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Repository\BlockRepositoryInterface;
use Adshares\AdsOperator\Document\Block;

/**
 * Class BlockRepository
 * @package Adshares\AdsOperator\Repository\Doctrine
 */
class BlockRepository extends BaseRepository implements BlockRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'time',
        ];
    }

    /**
     * @param string $blockId
     * @return Block|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getBlock(string $blockId):? Block
    {
        /** @var Block $block */
        $block = $this->find($blockId);

        return $block;
    }
}

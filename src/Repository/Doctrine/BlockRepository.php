<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Repository\BlockRepositoryInterface;

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
}

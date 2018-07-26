<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Repository\TransactionRepositoryInterface;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'blockId',
            'type',
        ];
    }
}

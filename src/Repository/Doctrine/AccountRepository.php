<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Repository\AccountRepositoryInterface;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
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

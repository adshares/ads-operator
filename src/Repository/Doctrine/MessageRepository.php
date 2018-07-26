<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Repository\MessageRepositoryInterface;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'blockId',
        ];
    }
}

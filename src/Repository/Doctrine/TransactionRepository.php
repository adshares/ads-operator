<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\Ads\Entity\Transaction\AbstractTransaction;
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

    /**
     * @param string $transactionId
     * @return AbstractTransaction|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getTransaction(string $transactionId):? AbstractTransaction
    {
        /** @var AbstractTransaction $transaction */
        $transaction = $this->find($transactionId);

        return $transaction;
    }
}

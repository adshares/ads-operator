<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\Ads\Entity\Transaction\AbstractTransaction;

/**
 * Interface TransactionRepositoryInterface
 * @package Adshares\AdsOperator\Repository
 */
interface TransactionRepositoryInterface extends ListRepositoryInterface
{
    /**
     * @param string $transactionId
     * @return AbstractTransaction
     */
    public function getTransaction(string $transactionId):? AbstractTransaction;
}

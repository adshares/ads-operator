<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Account;

/**
 * Interface AccountRepositoryInterface
 * @package Adshares\AdsOperator\Repository
 */
interface AccountRepositoryInterface extends ListRepositoryInterface
{
    /**
     * @param string $accountId
     * @return Account
     */
    public function getAccount(string $accountId):? Account;
}

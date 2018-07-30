<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Document\Account;
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

    /**
     * @param string $accountId
     * @return Account|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getAccount(string $accountId):? Account
    {
        /** @var Account $account */
        $account = $this->find($accountId);

        return $account;
    }
}

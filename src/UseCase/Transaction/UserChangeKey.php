<?php
/**
 * Copyright (C) 2018 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator.  If not, see <https://www.gnu.org/licenses/>
 */

namespace Adshares\AdsOperator\UseCase\Transaction;

use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\LocalTransaction;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\LocalTransactionRepositoryInterface;
use Adshares\AdsOperator\UseCase\Exception\AddressDoesNotBelongToUserException;
use Adshares\AdsOperator\UseCase\Exception\InvalidValueException;

class UserChangeKey
{
    const USER_CHANGE_ACCOUNT_KEY = 'changeAccountKey';

    /**
     * @var RunTransaction
     */
    private $runTransaction;

    /**
     * @var LocalTransactionRepositoryInterface
     */
    private $transactionRepository;

    public function __construct(RunTransaction $transaction, LocalTransactionRepositoryInterface $transactionRepository)
    {
        $this->runTransaction = $transaction;
        $this->transactionRepository = $transactionRepository;
    }

    public function change(User $user, string $address, string $publicKey, string $signature): LocalTransaction
    {
        if (!Account::validateId($address)) {
            throw new InvalidValueException('Address value is invalid.');
        }

        if (strlen($publicKey) !== 64 || !ctype_xdigit($publicKey)) {
            throw new InvalidValueException('Public key value is invalid.');
        }

        if (strlen($signature) !== 128 || !ctype_xdigit($signature)) {
            throw new InvalidValueException('Signature value is invalid.');
        }

        if (!$user->isMyAccount($address)) {
            throw new AddressDoesNotBelongToUserException(sprintf(
                'Address %s does not belong to user %s',
                $address,
                $user->getId()
            ));
        }

        $params = [
            'publicKey' => $publicKey,
            'signature' => $signature,
        ];

        $response = $this->runTransaction->run(self::USER_CHANGE_ACCOUNT_KEY, $address, $params);

        $transaction = new LocalTransaction(
            uniqid(),
            $user->getId(),
            self::USER_CHANGE_ACCOUNT_KEY,
            $response['hash'],
            $response['msid'],
            $response['data'],
            $params
        );

        $this->transactionRepository->add($transaction);

        return $transaction;
    }
}

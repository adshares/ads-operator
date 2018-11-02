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

use Adshares\AdsOperator\Document\LocalTransaction;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\LocalTransactionRepositoryInterface;
use Adshares\AdsOperator\UseCase\Exception\InvalidValueException;
use Adshares\AdsOperator\UseCase\Exception\UnauthorizedOperationException;

class ConfirmChangeUserKey
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

    public function confirm(User $user, string $signature, string $id): LocalTransaction
    {
        if (strlen($signature) !== 128 || !ctype_xdigit($signature)) {
            throw new InvalidValueException(sprintf('Signature value %s is invalid.', $signature));
        }

        $transaction = $this->transactionRepository->findById($id);

        if ($transaction->getUserId() !== $user->getId()) {
            throw new UnauthorizedOperationException(sprintf(
                'User %s is not authorized to confirm a transaction (user: %s, transaction: %s).',
                $user->getId(),
                $transaction->getUserId(),
                $transaction->getId()
            ));
        }

        $params = $transaction->getParams();

        $response = $this->runTransaction->run(
            self::USER_CHANGE_ACCOUNT_KEY,
            $transaction->getAddress(),
            $signature,
            $transaction->getTime(),
            $params
        );

        $transaction->setTransactionId($response->transactionId);

        $this->transactionRepository->modify($transaction);

        return $transaction;
    }
}

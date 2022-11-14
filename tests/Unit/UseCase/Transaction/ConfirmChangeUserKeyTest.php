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

declare(strict_types=1);

namespace Adshares\AdsOperator\Tests\Unit\UseCase\Transaction;

use Adshares\AdsOperator\Document\LocalTransaction;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\Exception\LocalTransactionNotFoundException;
use Adshares\AdsOperator\Repository\LocalTransactionRepositoryInterface;
use Adshares\AdsOperator\Tests\Unit\StringHelper;
use Adshares\AdsOperator\UseCase\Exception\InvalidValueException;
use Adshares\AdsOperator\UseCase\Exception\UnauthorizedOperationException;
use Adshares\AdsOperator\UseCase\Transaction\ConfirmChangeUserKey;
use Adshares\AdsOperator\UseCase\Transaction\RunTransaction;
use PHPUnit\Framework\TestCase;

class ConfirmChangeUserKeyTest extends TestCase
{
    public function testWhenSignatureIsInvalid()
    {
        $this->expectException(InvalidValueException::class);

        $signature = StringHelper::randHex(120); // 128 characters length is correct
        $confirmUserChangeKey = new ConfirmChangeUserKey(
            $this->createRunTransaction(),
            $this->createLocalTransactionRepository()
        );
        $user = new User('user@adshares.net', sha1('test'));

        $confirmUserChangeKey->confirm($user, $signature, uniqid());
    }

    public function testWhenLocalTransactionDoesNotExistInDatabase()
    {
        $this->expectException(LocalTransactionNotFoundException::class);

        $signature = StringHelper::randHex(128);
        $transactionRepository = $this->createLocalTransactionRepository();
        $transactionRepository
            ->expects($this->once())
            ->method('findById')
            ->will($this->throwException(new LocalTransactionNotFoundException('error')));

        $confirmUserChangeKey = new ConfirmChangeUserKey($this->createRunTransaction(), $transactionRepository);
        $user = new User('user@adshares.net', sha1('test'));

        $confirmUserChangeKey->confirm($user, $signature, uniqid());
    }

    public function testWhenUserIsUnauthorized()
    {
        $this->expectException(UnauthorizedOperationException::class);

        $signature = StringHelper::randHex(128);
        $transaction = $this->createLocalTransaction();

        $transactionRepository = $this->createLocalTransactionRepository();
        $transactionRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($transaction);

        $confirmUserChangeKey = new ConfirmChangeUserKey($this->createRunTransaction(), $transactionRepository);
        $user = new User('user@adshares.net', sha1('test'));
        $user->setId('1');

        $confirmUserChangeKey->confirm($user, $signature, uniqid());
    }

    private function createLocalTransaction()
    {
        return new LocalTransaction(
            uniqid(),
            '1234',
            '0001-00000001-0001',
            ConfirmChangeUserKey::USER_CHANGE_ACCOUNT_KEY,
            StringHelper::randHex(64),
            1,
            StringHelper::randHex(150),
            100000,
            new \DateTime(),
            []
        );
    }

    private function createRunTransaction()
    {
        return $this->createMock(RunTransaction::class);
    }

    private function createLocalTransactionRepository()
    {
        return $this->createMock(LocalTransactionRepositoryInterface::class);
    }
}

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

namespace Adshares\AdsOperator\Tests\Unit\UseCase\Transaction;

use Adshares\AdsOperator\Document\LocalTransaction;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\LocalTransactionRepositoryInterface;
use Adshares\AdsOperator\Tests\Unit\StringHelper;
use Adshares\AdsOperator\UseCase\Exception\AddressDoesNotBelongToUserException;
use Adshares\AdsOperator\UseCase\Exception\InvalidValueException;
use Adshares\AdsOperator\UseCase\Transaction\CommandResponse;
use Adshares\AdsOperator\UseCase\Transaction\RunTransaction;
use Adshares\AdsOperator\UseCase\Transaction\ChangeUserKey;
use PHPUnit\Framework\TestCase;

class ChangeUserKeyTest extends TestCase
{
    private $address = '0001-00000000-9B6F';

    public function testWhenAddressIsNotBelongToUser()
    {
        $this->expectException(AddressDoesNotBelongToUserException::class);

        $userChangeKey = new ChangeUserKey($this->createRunTransaction(), $this->createLocalTransactionRepository());
        $user = new User('user@adshares.net', sha1('test'));

        $userChangeKey->change($user, $this->address, StringHelper::randHex(64), StringHelper::randHex(128));
    }

    /**
     * @dataProvider getInputData
     */
    public function testWhenInputParametersAreInvalid($address, $publicKey, $signature)
    {
        $this->expectException(InvalidValueException::class);

        $userChangeKey = new ChangeUserKey($this->createRunTransaction(), $this->createLocalTransactionRepository());
        $user = new User('user@adshares.net', sha1('test'));

        $userChangeKey->change($user, $address, $publicKey, $signature);
    }

    public function getInputData()
    {
        $validPublicKey = StringHelper::randHex(64);
        $validSignature = StringHelper::randHex(128);

        $invalidPublicKeyLength = StringHelper::randHex(63);
        $invalidPublicKeyCharacters = StringHelper::randHex(62).'XX';
        $invalidSignatureLength = StringHelper::randHex(127);
        $invalidSignatureCharacters = StringHelper::randHex(126).'XX';

        return [
            [
                'ABCDEFG',
                $validPublicKey,
                $validSignature,
            ],
            [
                $this->address,
                $invalidPublicKeyLength,
                $validSignature,
            ],
            [
                $this->address,
                $invalidPublicKeyCharacters,
                $validSignature,
            ],
            [
                $this->address,
                $validPublicKey,
                $invalidSignatureLength,
            ],
            [
                $this->address,
                $validPublicKey,
                $invalidSignatureCharacters
            ],
        ];
    }

    public function testWhenInputDataAreValidAndAddressBelongsToUser()
    {
        $response = new CommandResponse(
            $this->address,
            '0901000000000001000000CD76B45B01A9D37766EF74C17C12D5666220741494AB4D49AF3015CE7C3685CA6560CF3E',
            1000000,
            'F209A4FF8CD27DABB5FADFAE84BB37D62B2DB5260129E6B8E35CFE4448C9C370',
            7
        );

        $response->setTime(new \DateTime());

        $runTransaction = $this->createRunTransaction();
        $runTransaction
            ->expects($this->once())
            ->method('dryRun')
            ->willReturn($response);

        $localTransactionRepository = $this->createLocalTransactionRepository();
        $localTransactionRepository
            ->expects($this->once())
            ->method('add');

        $user = new User('user@adshares.net', sha1('test'));
        $user->setId(uniqid());
        $user->addAccount($this->address);

        $publicKey = StringHelper::randHex(64);
        $signature = StringHelper::randHex(128);

        $userChangeKey = new ChangeUserKey($runTransaction, $localTransactionRepository);
        $transaction = $userChangeKey->change($user, $this->address, $publicKey, $signature);

        $this->assertInstanceOf(LocalTransaction::class, $transaction);
        $this->assertEquals($response->hash, $transaction->getHash());
        $this->assertEquals($response->msid, $transaction->getMsid());
        $this->assertEquals($response->data, $transaction->getData());
    }

    public function createRunTransaction()
    {
        return $this->createMock(RunTransaction::class);
    }

    private function createLocalTransactionRepository()
    {
        return $this->createMock(LocalTransactionRepositoryInterface::class);
    }
}

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

use Adshares\Ads\AdsClient;
use Adshares\Ads\Entity\Tx;
use Adshares\Ads\Response\ChangeAccountKeyResponse;
use Adshares\Ads\Response\GetAccountResponse;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Tests\Unit\StringHelper;
use Adshares\AdsOperator\UseCase\Exception\UnsupportedTransactionException;
use Adshares\AdsOperator\UseCase\Transaction\CommandResponse;
use Adshares\AdsOperator\UseCase\Transaction\RunTransaction;
use PHPUnit\Framework\TestCase;

class RunTransactionTest extends TestCase
{
    private $address = '0001-00000000-9B6F';

    public function testWhenTypeIsUnsupported()
    {
        $this->expectException(UnsupportedTransactionException::class);
        $type = 'unsupportedType';

        $transaction = new RunTransaction($this->createAdsClient());
        $transaction->run($type, $this->address, StringHelper::randHex(128), new \DateTime(), []);
    }

    public function testWhenChangeAccountKeyTransaction()
    {
        $type = 'changeAccountKey';
        $params = [
            'publicKey' => StringHelper::randHex(64),
            'signature' => StringHelper::randHex(128),
        ];

        $hash = StringHelper::randHex(64);
        $msid = 5;
        $fee = 1000000;
        $data = StringHelper::randHex(58);

        $rawData = [
            'hash' => $hash,
            'msid' => $msid,
            'fee' => $fee,
        ];

        $response = $this->createMock(ChangeAccountKeyResponse::class);



        $response
            ->expects($this->once())
            ->method('getTx')
            ->willReturn((new class extends Tx {
                public static function construct(string $data, int $fee)
                {
                    $x = new self();
                    $x->data = $data;
                    $x->fee = $fee;
                    return $x;
                }
            })::construct($data, $fee));

        $client = $this->addGetAccount($this->createAdsClient(), $rawData);
        $client
            ->expects($this->once())
            ->method('changeAccountKey')
            ->willReturn($response);

        $expected = new CommandResponse(
            $this->address,
            $data,
            $fee,
            $hash,
            $msid
        );

        $transaction = new RunTransaction($client);
        $result = $transaction->run($type, $this->address, StringHelper::randHex(128), new \DateTime(), $params);

        $this->assertEquals($expected, $result);
    }

    private function createAdsClient()
    {
        return $this->createMock(AdsClient::class);
    }

    private function addGetAccount($client, array $data)
    {
        $account = Account::createFromRawData($data);

        $accountResponse = $this->createMock(GetAccountResponse::class);
        $accountResponse
            ->expects($this->once())
            ->method('getAccount')
            ->willReturn($account);

        $client
            ->expects($this->once())
            ->method('getAccount')
            ->willReturn($accountResponse);

        return $client;
    }
}

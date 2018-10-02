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

use Adshares\Ads\AdsClient;
use Adshares\AdsOperator\UseCase\Exception\UnsupportedTransactionException;

class RunTransaction
{
    /**
     * @var AdsClient
     */
    private $client;

    public function __construct(AdsClient $adsClient)
    {
        $this->client = $adsClient;
    }

    /**
     * @param string $type
     * @param string $address
     * @param array $params
     * @param bool $isDry
     * @throws UnsupportedTransactionException
     * @return array
     *
     */
    public function run(string $type, string $address, array $params, bool $isDry = true)
    {
        $getAccountResponse = $this->client->getAccount($address);
        $account = $getAccountResponse->getAccount();

        $command = FactoryCommand::create($type, $params);

        $command->setSender($address);
        $command->setLastHash($account->getHash());
        $command->setLastMsid($account->getMsid());

        $response = $this->client->changeAccountKey($command, $isDry);

        return [
            'address' => $address,
            'data' => $response->getTx()->getData(),
            'hash' => $account->getHash(),
            'msid' => $account->getMsid(),
        ];
    }
}

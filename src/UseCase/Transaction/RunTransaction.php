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

namespace Adshares\AdsOperator\UseCase\Transaction;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CommandError;
use Adshares\Ads\Entity\Tx;
use Adshares\Ads\Exception\CommandException;
use Adshares\Ads\Response\AbstractResponse;
use Adshares\AdsOperator\UseCase\Exception\TooLowBalanceException;
use Adshares\AdsOperator\UseCase\Exception\TransactionCannotBeProceedException;
use Adshares\AdsOperator\UseCase\Exception\UnsupportedTransactionException;
use Adshares\AdsOperator\UseCase\Exception\AccountNotFoundException;

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
     * @throws UnsupportedTransactionException
     * @return CommandResponse
     *
     */
    public function dryRun(string $type, string $address, array $params): CommandResponse
    {
        $this->validate($type);
        $account = $this->getAccount($address);

        $command = CommandFactory::create($type, $params);
        $command->setSender($address);
        $command->setLastHash($account->getHash());
        $command->setLastMsid($account->getMsid());

        /** @var AbstractResponse $response */
        $response = $this->client->{$type}($command, true);

        /** @var Tx $tx */
        $tx = $response->getTx();

        $commandResponse = new CommandResponse(
            $address,
            $tx->getData(),
            $tx->getFee(),
            $account->getHash(),
            $account->getMsid()
        );

        $commandResponse->setTime($tx->getTime());

        return $commandResponse;
    }

    /**
     * @param string $type
     * @param string $address
     * @param string $signature
     * @param \DateTime $time
     * @param array $params
     * @throws UnsupportedTransactionException
     * @throws TooLowBalanceException
     * @throws TransactionCannotBeProceedException
     * @return CommandResponse
     */
    public function run(
        string $type,
        string $address,
        string $signature,
        \DateTime $time,
        array $params
    ): CommandResponse {
        $this->validate($type);
        $account = $this->getAccount($address);

        $command = CommandFactory::create($type, $params);
        $command->setSender($address);
        $command->setLastHash($account->getHash());
        $command->setLastMsid($account->getMsid());
        $command->setTimestamp($time->getTimestamp());
        $command->setSignature($signature);

        try {
            /** @var AbstractResponse $response */
            $response = $this->client->{$type}($command, false);
        } catch (CommandException $ex) {
            if ($ex->getCode() === CommandError::LOW_BALANCE) {
                throw new TooLowBalanceException('Too low balance on account.');
            }

            throw new TransactionCannotBeProceedException(
                sprintf('Transaction cannot be proceed for account %s', $address),
                0,
                $ex
            );
        }

        /** @var Tx $tx */
        $tx = $response->getTx();

        $commandResponse = new CommandResponse(
            $address,
            $tx->getData(),
            $tx->getFee(),
            $account->getHash(),
            $account->getMsid()
        );
        $commandResponse->setTransactionId($tx->getId());

        return $commandResponse;
    }

    private function validate(string $type): void
    {
        $methodName = ($type === 'sendOne' || $type === 'sendMany') ? 'runTransaction' : $type;

        if (!method_exists($this->client, $methodName)) {
            throw new UnsupportedTransactionException(sprintf('Unsupported transaction type: %s', $type));
        }
    }

    private function getAccount(string $address)
    {
        try {
            $getAccountResponse = $this->client->getAccount($address);
        } catch (CommandException $ex) {
            if ($ex->getCode() === CommandError::GET_USER_FAIL) {
                throw new AccountNotFoundException(sprintf('Failed to get data for account %s', $address));
            }

            throw new TransactionCannotBeProceedException(
                sprintf('Transaction cannot be proceed for account: %s', $address),
                0,
                $ex
            );
        }

        return $getAccountResponse->getAccount();
    }
}

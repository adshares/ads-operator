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

namespace Adshares\AdsOperator\AdsImporter;

use Adshares\Ads\Response\GetBlockResponse;
use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Info;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Transaction\ConnectionTransaction;
use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CommandError;
use Adshares\Ads\Response\GetMessageResponse;
use Adshares\Ads\Exception\CommandException;
use Adshares\AdsOperator\AdsImporter\Database\DatabaseMigrationInterface;
use Adshares\AdsOperator\Helper\NumericalTransformation;
use Psr\Log\LoggerInterface;

/**
 * Imports network's data using ADS Client.
 *
 * @package Adshares\AdsOperator\AdsImporter
 */
class Importer
{
    /**
     * @var AdsClient
     */
    private $client;

    /**
     * @var DatabaseMigrationInterface
     */
    private $databaseMigration;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $amountPrecision;

    /**
     * @var int
     */
    private $totalSupply;

    /**
     * @var int
     */
    private $genesisTime;

    /**
     * @var int
     */
    private $blockLength;

    /**
     * @var array
     */
    private $nonCirculatingAccounts;

    /**
     * @var ImporterResult
     */
    private $importerResult;

    /**
     * Importer constructor.
     * @param AdsClient $client
     * @param DatabaseMigrationInterface $databaseMigration
     * @param LoggerInterface $logger
     * @param int $totalSupply
     * @param int $amountPrecision
     * @param int $genesisTime
     * @param int $blockLength
     * @param string $nonCirculatingAccounts
     */
    public function __construct(
        AdsClient $client,
        DatabaseMigrationInterface $databaseMigration,
        LoggerInterface $logger,
        int $totalSupply,
        int $amountPrecision,
        int $genesisTime,
        int $blockLength,
        string $nonCirculatingAccounts
    ) {
        $this->client = $client;
        $this->databaseMigration = $databaseMigration;
        $this->logger = $logger;
        $this->totalSupply = $totalSupply;
        $this->amountPrecision = $amountPrecision;
        $this->genesisTime = $genesisTime;
        $this->blockLength = $blockLength;
        $this->nonCirculatingAccounts = array_filter(explode(',', $nonCirculatingAccounts));
        $this->importerResult = new ImporterResult();
    }

    /**
     * @return ImporterResult
     */
    public function import(): ImporterResult
    {
        $getMeResponse = $this->client->getMe();
        $startTime = $this->getStartTime();
        $endTime = (int)$getMeResponse->getPreviousBlockTime()->format('U');
        $blockId = NumericalTransformation::decToHex($startTime);

        do {
            try {
                $blockResponse = $this->client->getBlock($blockId);

                /** @var Block $block */
                $block = $blockResponse->getBlock();

                if ($block->getMessageCount() > 0) {
                    $blockTransactions = $this->addMessagesFromBlock($block);

                    $block->setTransactionCount($blockTransactions);
                }

                $this->databaseMigration->addBlock($block, $this->blockLength);
                ++$this->importerResult->blocks;
            } catch (CommandException $ex) {
                if ($ex->getCode() !== CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                    $this->addExceptionToLog($ex, sprintf('get_block (%s)', $blockId));
                }
            }

            $startTime += $this->blockLength;
            $blockId = NumericalTransformation::decToHex($startTime);
        } while ($startTime <= $endTime);


        try {
            $blockResponse = $this->client->getBlock();
            $this->updateNodes($blockResponse);
            $this->updateInfo($blockResponse);
        } catch (CommandException $ex) {
            if ($ex->getCode() !== CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                throw new AdsClientException('Cannot proceed importing data: '.$ex->getMessage());
            }
        }

        return $this->importerResult;
    }

    /**
     * @return int
     */
    private function getStartTime(): int
    {
        $from = $this->databaseMigration->getNewestBlockTime();

        if ($from) {
            return $from;
        }

        return $this->genesisTime;
    }

    /**
     * @param GetBlockResponse $blockResponse
     */
    private function updateNodes(GetBlockResponse $blockResponse): void
    {
        $nodes = $blockResponse->getBlock()->getNodes();

        /** @var Node $node */
        foreach ($nodes as $node) {
            if ($node->isSpecial()) {
                continue;
            }

            $node->setVersion($this->databaseMigration->getNodeVersion($node->getId()));
            $node->setTransactionCount($this->databaseMigration->getNodeTransactionCount($node->getId()));

            $this->updateAccounts($node);

            $this->databaseMigration->addOrUpdateNode($node);
            ++$this->importerResult->nodes;
        }

        return;
    }

    /**
     * @param Node $node
     */
    private function updateAccounts(Node $node): void
    {
        $accountResponse = $this->client->getAccounts($node->getId());
        $accounts = $accountResponse->getAccounts();

        /** @var Account $account */
        foreach ($accounts as $account) {
            $account->setTransactionCount($this->databaseMigration->getAccountTransactionCount($account->getAddress()));
            $this->databaseMigration->addOrUpdateAccount($account, $node);
            ++$this->importerResult->accounts;
        }
    }

    /**
     * @param Block $block
     * @return int
     */
    private function addMessagesFromBlock(Block $block): int
    {
        $blockTransactionsCount = 0;

        try {
            $messageIdResponse = $this->client->getMessageIds($block->getId());
            $messageIds = $messageIdResponse->getMessageIds();

            foreach ($messageIds as $messageId) {
                $messageResponse = $this->getMessageResponse($messageId, $block);

                if (!$messageResponse) {
                    continue;
                }

                /** @var Message $message */
                $message = $messageResponse->getMessage();
                $transactions = $messageResponse->getTransactions();
                $transactionsCount = count($transactions);

                $message->setTransactionCount($transactionsCount);
                $this->databaseMigration->addMessage($message);
                ++$this->importerResult->messages;

                if ($transactions) {
                    $this->addTransactionsFromMessage($message, $transactions);
                    $blockTransactionsCount += $transactionsCount;
                }
            }
        } catch (CommandException $ex) {
            $this->addExceptionToLog($ex, 'get_message_ids', $block);
        }

        return $blockTransactionsCount;
    }

    /**
     * @param string $messageId
     * @param Block $block
     * @return GetMessageResponse|null
     */
    private function getMessageResponse(string $messageId, Block $block):? GetMessageResponse
    {
        try {
            return $this->client->getMessage($messageId, $block->getId());
        } catch (CommandException $ex) {
            $this->addExceptionToLog($ex, sprintf('get_message (%s)', $messageId), $block);
        }

        return null;
    }

    /**
     * @param Message $message
     * @param array $transactions
     */
    private function addTransactionsFromMessage(Message $message, array $transactions): void
    {
        /** @var ArrayableInterface $transaction */
        foreach ($transactions as $transaction) {
            if ($transaction instanceof ConnectionTransaction) {
                $transaction->setTime($message->getTime());
            }
            $this->databaseMigration->addTransaction($transaction);
            ++$this->importerResult->transactions;
        }
    }

    /**
     * @param GetBlockResponse $blockResponse
     */
    private function updateInfo(GetBlockResponse $blockResponse): void
    {
        $info = new Info($this->genesisTime, $this->blockLength);

        $info->setLastBlockId($blockResponse->getBlock()->getId());
        $info->setTotalSupply($this->totalSupply / 10 ** $this->amountPrecision);

        $supply = 0;
        /** @var Node $node */
        foreach ($blockResponse->getBlock()->getNodes() as $node) {
            if ($node->isSpecial()) {
                continue;
            }
            $supply += $node->getBalance();
        }

        $circulatingSupply = $supply;
        foreach ($this->nonCirculatingAccounts as $address) {
            /** @var Account $account */
            $account = $this->client->getAccount($address)->getAccount();
            if ($account !== null) {
                $circulatingSupply -= $account->getBalance();
            }
        }

        $info->setCirculatingSupply($circulatingSupply / 10 ** $this->amountPrecision);
        $info->setUnpaidDividend(($this->totalSupply - $supply) / 10 ** $this->amountPrecision);

        $this->databaseMigration->addOrUpdateInfo($info);
    }

    /**
     * @param CommandException $exception
     * @param string $message
     * @param Block|null $block
     */
    private function addExceptionToLog(CommandException $exception, string $message, ?Block $block = null): void
    {
        $context = [
            'error_code' => $exception->getCode(),
            'error_message' => CommandError::getMessageByCode($exception->getCode()),
        ];

        if ($block) {
            $context['block'] = $block->getId();
        }

        $pattern = '[ADS Synchronization] %s - %s';

        $this->logger->error(sprintf($pattern, $message, $exception->getMessage()), $context);
    }

    /**
     * @return ImporterResult
     */
    public function getResult(): ImporterResult
    {
        return $this->importerResult;
    }
}

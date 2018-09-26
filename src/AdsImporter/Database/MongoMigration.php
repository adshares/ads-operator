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

namespace Adshares\AdsOperator\AdsImporter\Database;

use Adshares\Ads\Entity\Transaction\SendManyTransactionWire;
use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Transaction\LogAccountTransaction;
use Adshares\AdsOperator\Document\Transaction\SendManyTransaction;
use Adshares\AdsOperator\Document\Transaction\SendOneTransaction;
use Doctrine\MongoDB\Connection;
use MongoDB\BSON\UTCDateTime;
use Doctrine\MongoDB\Collection;
use Psr\Log\LoggerInterface;

/**
 * Class MongoMigration
 *
 * @package Adshares\AdsOperator\AdsImporter\Database
 */
class MongoMigration implements DatabaseMigrationInterface
{
    const BLOCK_COLLECTION = 'block';
    const MESSAGE_COLLECTION = 'message';
    const TRANSACTION_COLLECTION = 'transaction';
    const NODE_COLLECTION = 'node';
    const ACCOUNT_COLLECTION = 'account';
    const ACCOUNT_TRANSACTION_COLLECTION = 'account_transaction';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var \Doctrine\MongoDB\Database
     */
    private $db;

    /**
     * @var Collection
     */
    private $blockCollection;

    /**
     * @var Collection
     */
    private $messageCollection;

    /**
     * @var Collection
     */
    private $transactionCollection;

    /**
     * @var Collection
     */
    private $nodeCollection;

    /**
     * @var Collection
     */
    private $accountCollection;

    /**
     * @var Collection
     */
    private $accountTransactionCollection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MongoMigration constructor.
     *
     * @param Connection $connection
     * @param string $databaseName
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $connection, string $databaseName, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
        $this->db = $this->connection->selectDatabase($databaseName);

        $this->selectCollections();
    }

    /**
     * @return void
     */
    private function selectCollections(): void
    {
        $this->blockCollection = $this->db->selectCollection(self::BLOCK_COLLECTION);
        $this->messageCollection = $this->db->selectCollection(self::MESSAGE_COLLECTION);
        $this->transactionCollection = $this->db->selectCollection(self::TRANSACTION_COLLECTION);
        $this->nodeCollection = $this->db->selectCollection(self::NODE_COLLECTION);
        $this->accountCollection = $this->db->selectCollection(self::ACCOUNT_COLLECTION);
        $this->accountTransactionCollection = $this->db->selectCollection(self::ACCOUNT_TRANSACTION_COLLECTION);
    }

    /**
     * @param Message $message
     * @return void
     */
    public function addMessage(Message $message): void
    {
        $document = [
            '_id' => $message->getId(),
            'nodeId' => $message->getNodeId(),
            'blockId' => $message->getBlockId(),
            'transactionCount' => $message->getTransactionCount(),
            'hash' => $message->getHash(),
            'length' => $message->getLength(),
        ];

        $this->messageCollection->update(['_id' => $message->getId()], $document, ['upsert' => true]);
    }

    /**
     * @param Block $block
     * @return void
     */
    public function addBlock(Block $block): void
    {
        $document = [
            '_id' => $block->getId(),
            'dividendBalance' => $block->getDividendBalance(),
            'dividendPay' => $block->isDividendPay(),
            'messageCount' => $block->getMessageCount(),
            'minHash' => $block->getMinHash(),
            'msgHash' => $block->getMsgHash(),
            'nowHash' => $block->getNowHash(),
            'oldHash' => $block->getOldHash(),
            'vipHash' => $block->getVipHash(),
            'nodeCount' => $block->getNodeCount(),
            'time' => $this->createMongoDate($block->getTime()),
            'voteYes' => $block->getVoteYes(),
            'voteNo' => $block->getVoteNo(),
            'voteTotal' => $block->getVoteTotal(),
            'transactionCount' => $block->getTransactionCount(),
        ];

        $this->blockCollection->update(['_id' => $block->getId()], $document, ['upsert' => true]);
    }

    /**
     * @param ArrayableInterface $transaction
     */
    public function addTransaction(ArrayableInterface $transaction): void
    {
        $document = $transaction->toArray();

        if ($transaction instanceof LogAccountTransaction) {
            $networkAccount = $document['networkAccount'];

            $networkAccount['localChange'] = $this->createMongoDate($networkAccount['localChange']);
            $networkAccount['remoteChange'] = $this->createMongoDate($networkAccount['remoteChange']);
            $networkAccount['time'] = $this->createMongoDate($networkAccount['time']);

            $document['networkAccount'] = $networkAccount;
        }

        if (isset($document['time']) && $document['time'] instanceof \DateTime) {
            $document['time'] = $this->createMongoDate($document['time']);
        }

        $this->transactionCollection->update(['_id' => $document['_id']], $document, ['upsert' => true]);

        if ($transaction instanceof SendOneTransaction) {
            $data = [];

            $data[] = $this->getTransactionEntry($transaction->getSenderAddress(), $transaction->getId());

            if ($transaction->getSenderAddress() !== $transaction->getTargetAddress()) {
                $data[] = $this->getTransactionEntry($transaction->getTargetAddress(), $transaction->getId());
            }

            $this->accountTransactionCollection->batchInsert($data);
        }

        if ($transaction instanceof SendManyTransaction) {
            $data = [];

            $data[] = $this->getTransactionEntry($transaction->getSenderAddress(), $transaction->getId());

            /** @var SendManyTransactionWire $singleTransaction */
            foreach ($transaction->getWires() as $singleTransaction) {
                $data[] = $this->getTransactionEntry($singleTransaction->getTargetAddress(), $transaction->getId());
            }

            $this->accountTransactionCollection->batchInsert($data);
        }
    }

    /**
     * @param string $accountAddress
     * @param string $transactionId
     * @return array
     */
    private function getTransactionEntry(string $accountAddress, string $transactionId): array
    {
        return [
            'accountId' => $accountAddress,
            'transactionId' => $transactionId,
        ];
    }

    /**
     * @param Node $node
     */
    public function addOrUpdateNode(Node $node): void
    {
        $document = [
            '_id' => $node->getId(),
            'accountCount' => $node->getAccountCount(),
            'balance' => $node->getBalance(),
            'hash' => $node->getHash(),
            'messageHash' => $node->getMessageHash(),
            'ipv4' => $node->getIpv4(),
            'msid' => $node->getMsid(),
            'mtim' => $this->createMongoDate($node->getMtim()),
            'port' => $node->getPort(),
            'publicKey' => $node->getPublicKey(),
            'status' => $node->getStatus(),

        ];

        $this->nodeCollection->update(['_id' => $node->getId()], $document, ['upsert' => true]);
    }

    /**
     * @param Account $account
     * @param Node $node
     */
    public function addOrUpdateAccount(Account $account, Node $node): void
    {
        $document = [
            '_id' => $account->getAddress(),
            'nodeId' => $account->getNodeId(),
            'pairedNode' => $account->getPairedNodeId(),
            'address' => $account->getAddress(),
            'balance' => $account->getBalance(),
            'hash' => $account->getHash(),
            'localChange' => $this->createMongoDate($account->getLocalChange()),
            'remoteChange' => $this->createMongoDate($account->getRemoteChange()),
            'time' => $this->createMongoDate($account->getTime()),
            'msid' => $account->getMsid(),
            'pairedAddress' => $account->getPairedAddress(),
            'publicKey' => $account->getPublicKey(),
            'status' => $account->getStatus(),
        ];

        $this->accountCollection->update(['_id' => $account->getAddress()], $document, ['upsert' => true]);
    }

    /**
     * Gets newest block's time from database.
     *
     * @return int|null
     */
    public function getNewestBlockTime(): ?int
    {
        $collection = $this->db->selectCollection(self::BLOCK_COLLECTION);
        $cursor = $collection->find()->sort(['time' => -1])->limit(1);
        $cursor->next();
        $document = $cursor->current();

        if ($document) {
            return $document['time']->sec;
        }

        return null;
    }

    /**
     * @param \DateTime $date
     * @return UTCDateTime
     */
    private function createMongoDate(\DateTime $date): UTCDateTime
    {
        return new UTCDateTime((int)$date->format('U')*1000);
    }
}

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
use Adshares\AdsOperator\Document\Info;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Transaction\LogAccountTransaction;
use Adshares\AdsOperator\Document\Transaction\SendManyTransaction;
use Adshares\AdsOperator\Document\Transaction\SendOneTransaction;
use Doctrine\MongoDB\Connection;
use Doctrine\MongoDB\Cursor;
use MongoDB\BSON\UTCDateTime;
use Doctrine\MongoDB\Collection;
use phpDocumentor\Reflection\Types\Iterable_;
use Psr\Log\LoggerInterface;

/**
 * Class MongoMigration
 *
 * @package Adshares\AdsOperator\AdsImporter\Database
 */
class MongoMigration implements DatabaseMigrationInterface
{
    const INFO_COLLECTION = 'info';
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
    private $infoCollection;

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

    private $mongoUpdateOptions = [
        'upsert' => true,
    ];

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
        $this->infoCollection = $this->db->selectCollection(self::INFO_COLLECTION);
        $this->blockCollection = $this->db->selectCollection(self::BLOCK_COLLECTION);
        $this->messageCollection = $this->db->selectCollection(self::MESSAGE_COLLECTION);
        $this->transactionCollection = $this->db->selectCollection(self::TRANSACTION_COLLECTION);
        $this->nodeCollection = $this->db->selectCollection(self::NODE_COLLECTION);
        $this->accountCollection = $this->db->selectCollection(self::ACCOUNT_COLLECTION);
        $this->accountTransactionCollection = $this->db->selectCollection(self::ACCOUNT_TRANSACTION_COLLECTION);
    }

    /**
     * @param Info $info
     */
    public function addOrUpdateInfo(Info $info): void
    {
        $document = [
            '_id' => $info->getGenesisTime(),
            'blockLength' => $info->getBlockLength(),
            'lastBlockId' => $info->getLastBlockId(),
            'totalSupply' => $info->getTotalSupply(),
            'circulatingSupply' => $info->getCirculatingSupply(),
            'unpaidDividend' => $info->getUnpaidDividend(),
        ];

        $this->infoCollection->update(['_id' => $info->getGenesisTime()], $document, $this->mongoUpdateOptions);
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
            'time' => $this->createMongoDate($message->getTime()),
        ];

        $this->messageCollection->update(['_id' => $message->getId()], $document, $this->mongoUpdateOptions);
    }

    /**
     * @param Block $block
     * @param int $blockLength
     */
    public function addBlock(Block $block, int $blockLength): void
    {
        try {
            $endTime = clone $block->getTime();
            $endTime->add(
                new \DateInterval(sprintf('PT%dS', $blockLength))
            );
            $endTime = $this->createMongoDate($endTime);
        } catch (\Exception $e) {
            $endTime = null;
        }

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
            'endTime' => $endTime,
            'voteYes' => $block->getVoteYes(),
            'voteNo' => $block->getVoteNo(),
            'voteTotal' => $block->getVoteTotal(),
            'transactionCount' => $block->getTransactionCount(),
        ];

        $this->blockCollection->update(['_id' => $block->getId()], $document, $this->mongoUpdateOptions);
    }

    public function getBlock($blockId)
    {
        return $this->blockCollection->findOne(['_id' => $blockId]);
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

        $this->transactionCollection->update(['_id' => $document['_id']], $document, $this->mongoUpdateOptions);

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
            'messageCount' => $node->getMessageCount(),
            'transactionCount' => $node->getTransactionCount(),
            'balance' => $node->getBalance(),
            'hash' => $node->getHash(),
            'messageHash' => $node->getMessageHash(),
            'ipv4' => $node->getIpv4(),
            'msid' => $node->getMsid(),
            'mtim' => $this->createMongoDate($node->getMtim()),
            'port' => $node->getPort(),
            'publicKey' => $node->getPublicKey(),
            'status' => $node->getStatus(),
            'version' => $node->getVersion(),

        ];

        $this->nodeCollection->update(['_id' => $node->getId()], $document, $this->mongoUpdateOptions);
    }

    /**
     * @param string $nodeId
     * @return null|string
     */
    public function getNodeVersion(string $nodeId): ?string
    {
        $cursor = $this->transactionCollection->find([
            'type' => 'connection',
            'nodeId' => $nodeId,
        ])->sort(['_id' => -1])->limit(1);

        foreach ($cursor as $connection) {
            return $connection['version'] ?? null;
        }

        return null;
    }

    /**
     * @param string $nodeId
     * @return int
     */
    public function getNodeTransactionCount(string $nodeId): int
    {
        $cursor = $this->transactionCollection->find([
            'nodeId' => $nodeId,
        ]);

        return $cursor->count();
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
            'messageCount' => $account->getMessageCount(),
            'transactionCount' => $account->getTransactionCount(),
            'hash' => $account->getHash(),
            'localChange' => $this->createMongoDate($account->getLocalChange()),
            'remoteChange' => $this->createMongoDate($account->getRemoteChange()),
            'time' => $this->createMongoDate($account->getTime()),
            'msid' => $account->getMsid(),
            'pairedAddress' => $account->getPairedAddress(),
            'publicKey' => $account->getPublicKey(),
            'status' => $account->getStatus(),
        ];

        $this->accountCollection->update(['_id' => $account->getAddress()], $document, $this->mongoUpdateOptions);
    }

    /**
     * @param string $accountId
     * @return int
     */
    public function getAccountTransactionCount(string $accountId): int
    {
        $cursor = $this->accountTransactionCollection->find([
            'accountId' => $accountId,
        ]);

        return $cursor->count();
    }

    /**
     * @param string $accountId
     *
     * @return Cursor
     */
    public function getAccountTransactions(string $accountId): Cursor
    {
        $queryBuilder = $this->transactionCollection->createQueryBuilder();
        $cursor = $queryBuilder
            ->field('type')->notEqual('connection')
            ->addOr($queryBuilder->expr()->field('senderAddress')->equals($accountId))
            ->addOr($queryBuilder->expr()->field('targetAddress')->equals($accountId))
            ->addOr($queryBuilder->expr()->field('wires.targetAddress')->equals($accountId))
            ->sort('time', 1)
            ->getQuery()
            ->execute();


        return $cursor;
    }


    /**
     * Gets newest block's time from database.
     *
     * @return int|null
     */
    public function getNewestBlockTime(): ?int
    {
        $collection = $this->db->selectCollection(self::INFO_COLLECTION);
        $cursor = $collection->find()->sort(['lastBlockId' => -1])->limit(1);
        $cursor->next();
        $document = $cursor->current();

        if ($document) {
            return hexdec($document['lastBlockId']);
        }

        return null;
    }

    /**
     * @param \DateTime $date
     * @return UTCDateTime
     */
    private function createMongoDate(\DateTime $date): UTCDateTime
    {
        return new UTCDateTime((int)$date->format('U') * 1000);
    }

    public function getAllAccounts(): Cursor
    {
        return $this->accountCollection->find();
    }

    public function getTransaction($txid)
    {
        return $this->transactionCollection->findOne(['_id' => $txid]);
    }

    public function deleteAccountTransaction($id)
    {
        return $this->accountTransactionCollection->remove(['_id' => $id]);
    }
}

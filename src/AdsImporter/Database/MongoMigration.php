<?php


namespace Adshares\AdsOperator\AdsImporter\Database;

use Adshares\Ads\Entity\Transaction\SendManyTransactionWire;
use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Transaction\SendManyTransaction;
use Adshares\AdsOperator\Document\Transaction\SendOneTransaction;
use Doctrine\MongoDB\Connection;
use MongoDB\BSON\UTCDateTime;
use Doctrine\MongoDB\Collection;

class MongoMigration implements DatabaseMigrationInterface
{
    const BLOCKEXPLORER_DATABASE = 'blockexplorer';
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


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->db = $this->connection->selectDatabase(self::BLOCKEXPLORER_DATABASE);

        $this->prepareCollections();
    }

    private function prepareCollections(): void
    {
        $this->blockCollection = $this->db->createCollection(self::BLOCK_COLLECTION);
        $this->messageCollection = $this->db->createCollection(self::MESSAGE_COLLECTION);
        $this->transactionCollection = $this->db->createCollection(self::TRANSACTION_COLLECTION);
        $this->nodeCollection = $this->db->createCollection(self::NODE_COLLECTION);
        $this->accountCollection = $this->db->createCollection(self::ACCOUNT_COLLECTION);
        $this->accountTransactionCollection = $this->db->createCollection(self::ACCOUNT_TRANSACTION_COLLECTION);
    }

    public function addMessage(Message $message): void
    {
        $document = [
            "id" => $message->getMessageId(),
            "nodeId" => $message->getNodeId(),
            "blockId" => $message->getBlockId(),
            "transactionCount" => $message->getTransactionCount(),
            "hash" => $message->getHash(),
            "length" => $message->getLength(),
        ];

        $this->messageCollection->insert($document);
    }

    public function addBlock(Block $block): void
    {
        $document = [
            "id" => $block->getId(),
            "dividendBalance" => $block->getDividendBalance(),
            "messageCount" => $block->getMessageCount(),
            "minHash" => $block->getMinHash(),
            "msgHash" => $block->getMsgHash(),
            "nodeCount" => $block->getNodeCount(),
            "nowHash" => $block->getNowHash(),
            "oldHash" => $block->getOldHash(),
            "time" => new UTCDateTime((int)$block->getTime()->format('U')*1000),
            "vipHash" => $block->getVipHash(),
            "voteNo" => $block->getVoteNo(),
            "voteTotal" => $block->getVoteTotal(),
            "voteYes" => $block->getVoteYes(),
            "transactionCount" => $block->getTransactionCount(),
        ];

        try {
            $this->blockCollection->insert($document);
        } catch (\MongoDuplicateKeyException $ex) {
            // do nothing when a block exists in the database
        }
    }

    public function addTransaction(ArrayableInterface $transaction): void
    {
        $document = $transaction->toArray();

        try {
            $this->transactionCollection->insert($document);
        } catch (\Exception $ex) {
            // do nothing when a block exists in the database
        }

        if ($transaction instanceof SendOneTransaction) {
            $data = [];

            $data[] = [
                "accountId" => $transaction->getSenderAddress(),
                "transactionId" => $transaction->getId(),
            ];

            if ($transaction->getSenderAddress() !== $transaction->getTargetAddress()) {
                $data[] = [
                    "accountId" => $transaction->getTargetAddress(),
                    "transactionId" => $transaction->getId(),
                ];
            }

            $this->accountTransactionCollection->batchInsert($data);
        }

        if ($transaction instanceof SendManyTransaction) {
            $data = [];

            $data[] = [
                "accountId" => $transaction->getSenderAddress(),
                "transactionId" => $transaction->getId(),
            ];

            /** @var SendManyTransactionWire $singleTransaction */
            foreach ($transaction->getWires() as $singleTransaction) {
                $data[] = [
                    "accountId" => $singleTransaction->getTargetAddress(),
                    "transactionId" => $transaction->getId(),
                ];
            }

            $this->accountTransactionCollection->batchInsert($data);
        }
    }

    public function addOrUpdateNode(Node $node): void
    {
        $document = [
            "id" => $node->getId(),
            "accountCount" => $node->getAccountCount(),
            "ip" => $node->getIpv4(),
            "packCount" => $node->getMsid(),
            "port" => $node->getPort(),
            "publicKey" => $node->getPublicKey(),
            "balance" => $node->getBalance(),
        ];

        $this->nodeCollection->update(["id" => $node->getId()], $document, ['upsert' => true]);
    }

    public function addOrUpdateAccount(Account $account, Node $node): void
    {
        $document = [
            "address" => $account->getAddress(),
            "balance" => $account->getBalance(),
            "nodeId" => $account->getNode(),
            "number" => $account->getId(),
            "publicKey" => $account->getPublicKey(),
            "hash" => $account->getHash(),
            "time" => $account->getTime(),
            "nonce" => $account->getMsid(),
        ];

        $this->accountCollection->update(["address" => $account->getAddress()], $document, ['upsert' => true]);
    }

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
}

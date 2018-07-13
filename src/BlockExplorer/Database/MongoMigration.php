<?php


namespace Adshares\AdsManager\BlockExplorer\Database;

use Adshares\Ads\Entity\Account;
use Adshares\Ads\Entity\Node;
use Adshares\AdsManager\Document\Block;
use Adshares\AdsManager\Document\Package;
use Adshares\AdsManager\Document\Transaction;
use Adshares\AdsManager\Helper\NumericalTransformation;
use Doctrine\MongoDB\Connection;
use MongoDB\BSON\UTCDateTime;

class MongoMigration implements DatabaseMigrationInterface
{
    const BLOCKEXPLORER_DATABASE = 'blockexplorer';
    const BLOCK_COLLECTION = 'block';
    const PACKAGE_COLLECTION = 'package';
    const TRANSACTION_COLLECTION = 'transaction';
    const NODE_COLLECTION = 'node';
    const ACCOUNT_COLLECTION = 'account';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var \Doctrine\MongoDB\Database
     */
    private $db;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->db = $this->connection->selectDatabase(self::BLOCKEXPLORER_DATABASE);
    }

    public function addPackage(Package $package, Block $block): void
    {
        $collection = $this->db->createCollection(self::PACKAGE_COLLECTION);
        $document = [
            "id" => $this->generatePackageId($package),
            "nodeId" => $package->getNode(),
            "number" => $package->getNodeMsid(),
            "blockId" => $block->getId(),
            "transactionCount" => $package->getTransactionCount(),
        ];

        $collection->insert($document);
    }

    public function addBlock(Block $block): void
    {
        $collection = $this->db->createCollection(self::BLOCK_COLLECTION);
        $document = [
            "id" => $block->getId(),
//            "dividendBalance" => $block->getDividendBalance(),
            "messageCount" => $block->getMessageCount(),
            "messageHash" => $block->getMessageCount(),
            "minHash" => $block->getMinhash(),
            "msgHash" => $block->getMsghash(),
            "nodeCount" => $block->getNodeCount(),
            "nowHash" => $block->getNowhash(),
            "oldHash" => $block->getOldhash(),
            "time" => new UTCDateTime((int)$block->getTime()->format('U')*1000),
            "vipHash" => $block->getViphash(),
            "voteNo" => $block->getVoteNo(),
            "voteTotal" => $block->getVoteTotal(),
            "voteYes" => $block->getVoteYes(),
            "timestamp" => NumericalTransformation::hexToDec($block->getId()),
            "transactionCount" => $block->getTransactionCount(),
        ];

        try {
            $collection->insert($document);
        } catch (\MongoDuplicateKeyException $ex) {
            // do nothing when a block exists in the database
        }
    }

    public function addTransaction(Transaction $transaction): void
    {
        $collection = $this->db->createCollection(self::TRANSACTION_COLLECTION);
        $document = [
            "id" => $transaction->getId(),
            "blockId" => $transaction->getBlockId(),
            "packageId" => $transaction->getPackageId(),
            "data" => $transaction->getData(),
            "size" => $transaction->getSize(),
            "type" => $transaction->getType()
        ];

        try {
            $collection->insert($document);
        } catch (\Exception $ex) {
            // do nothing when a block exists in the database
        }
    }

    public function addOrUpdateNode(Node $node): void
    {
        $collection = $this->db->createCollection(self::NODE_COLLECTION);

        $document = [
            "id" => $node->getId(),
            "accountCount" => $node->getAccountCount(),
            "ip" => $node->getIpv4(),
            "packCount" => $node->getMsid(),
            "port" => $node->getPort(),
            "publicKey" => $node->getPublicKey(),
            "balance" => $node->getBalance(),
        ];

        $collection->update(["id" => $node->getId()], $document, ['upsert' => true]);
    }

    public function addOrUpdateAccount(Account $account, Node $node): void
    {
        $collection = $this->db->createCollection(self::ACCOUNT_COLLECTION);

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

        $collection->update(["address" => $account->getAddress()], $document, ['upsert' => true]);
    }

    public function getNewestBlockTime(): ?int
    {
        $collection = $this->db->selectCollection('block');
        $cursor = $collection->find()->sort(['timestamp' => -1])->limit(1);
        $cursor->next();
        $document = $cursor->current();

        if ($document) {
            return $document['time']->sec;
        }

        return null;
    }

    public function generatePackageId(Package $package): string
    {
        return str_pad(
            dechex(((int)$package->getNode() << 32) + $package->getNodeMsid()),
            12,
            '0',
            STR_PAD_LEFT
        );
    }
}

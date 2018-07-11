<?php


namespace Adshares\AdsManager\BlockExplorer\Database;

use Adshares\Ads\Entity\Block;
use Adshares\Ads\Entity\Package;
use Adshares\AdsManager\Helper\NumericalTransformation;
use Doctrine\MongoDB\Connection;
use MongoDB\BSON\UTCDateTime;

class MongoMigration implements DatabaseMigrationInterface
{
    const BLOCKEXPLORER_DATABASE = 'blockexplorer';
    const BLOCK_COLLECTION = 'block';
    const PACKAGE_COLLECTION = 'package';

    private $connection;
    private $db;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->db = $this->connection->selectDatabase(self::BLOCKEXPLORER_DATABASE);
    }

    public function addPackageToDatabase(Package $package, Block $block): void
    {
        $collection = $this->db->createCollection(self::PACKAGE_COLLECTION);
        $document = [
            "id" => $this->generatePackageId($package),
            "nodeId" => $package->getNode(),
            "number" => $package->getNodeMsid(),
            "blockId" => $block->getId(),
        ];

        $collection->insert($document);
    }

    private function generatePackageId(Package $package)
    {
        return str_pad(
            dechex(((int)$package->getNode() << 32) + $package->getNodeMsid()),
            12,
            '0',
            STR_PAD_LEFT
        );
    }

    public function addBlockToDatabase(Block $block): void
    {
        $collection = $this->db->createCollection(self::BLOCK_COLLECTION);
        $document = [
            "id" => $block->getId(),
            "dividendBalance" => $block->getDividendBalance(),
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
        ];

        try {
            $collection->insert($document);
        } catch (\MongoDuplicateKeyException $ex) {
            // do nothing when a block exists in the database
        }
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
}

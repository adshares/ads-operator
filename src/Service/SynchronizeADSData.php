<?php


namespace Adshares\AdsManager\Service;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CommandError;
use Adshares\Ads\Entity\Block;
use Adshares\Ads\Entity\Package;
use Adshares\Ads\Exception\CommandException;
use Adshares\Ads\Response\GetPackageListResponse;
use Adshares\AdsManager\BlockExplorer\Database\DatabaseMigrationInterface;
use Adshares\AdsManager\Helper\NumericalTransformation;

class SynchronizeADSData
{
    /**
     * @var AdsClient
     */
    private $client;

    /**
     * @var DatabaseMigrationInterface
     */
    private $databaseMigration;

    private $genesisTime;

    private $newBlockTime = 32;

    public function __construct(AdsClient $client, DatabaseMigrationInterface $databaseMigration)
    {
        $this->client = $client;
        $this->databaseMigration = $databaseMigration;
        $this->genesisTime = 1531289536;
    }

    public function sync(): void
    {
        // db.getCollection("block").createIndex({ "id": 1 }, { "unique": true })

//        $this->client->getBlocks();
//        $id = '5B45A020';
//        $blockResponse = $this->client->getBlock($id);
//        $this->addPackagesFromBlock($blockResponse->getBlock());
//
//        die();
//        try {
//            $this->client->getBlocks();
//        } catch (CommandException $ex) {
//            if ($ex->getCode() === CommandError::NO_NEW_BLOCKS) {
//                return;
//            }
//        }


        $getMeResponse = $this->client->getMe();

        $actualBlockTime = (int)$getMeResponse->getCurrentBlockTime()->format('U');
        $from = $this->databaseMigration->getNewestBlockTime();

        if (!$from) {
            $from = $this->genesisTime;
        }

        $hexTime = NumericalTransformation::decToHex($from);
        do {
            try {
                $blockResponse = $this->client->getBlock($hexTime);
                $block = $blockResponse->getBlock();

                if ($block instanceof Block) {
                    $this->databaseMigration->addBlockToDatabase($block);
                    $this->addPackagesFromBlock($block);
                }
            } catch (CommandException $ex) {
                if ($ex->getCode() === CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                    // do nothing
                }
            }

            print("FROM: ".$from." ACTUAL:".$actualBlockTime."\n");

            $from += $this->newBlockTime;
            $hexTime = NumericalTransformation::decToHex($from);
        } while ($from <= $actualBlockTime);
    }


    private function addPackagesFromBlock(Block $block)
    {
        $packagesResponse = $this->client->getPackageList($block->getId());

        if ($packagesResponse instanceof GetPackageListResponse) {
            $packages = $packagesResponse->getPackages();
            /** @var Package $package */
            foreach ($packages as $package) {
                $this->databaseMigration->addPackageToDatabase($package, $block);
//                $packageResponse = $this->client->getPackage(
//                    $package->getNode(),
//                    $package->getNodeMsid(),
//                    $block->getId()
//                );
            }
        }
    }
}

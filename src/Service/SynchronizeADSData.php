<?php


namespace Adshares\AdsManager\Service;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Exception\CommandException;
use Adshares\Ads\Response\GetBlockResponse;
use Adshares\Ads\Response\GetBlocksResponse;
use Adshares\Ads\Response\GetPackageListResponse;
use Doctrine\MongoDB\Connection;

class SynchronizeADSData
{
    /**
     * @var AdsClient
     */
    private $client;

    /**
     * @var Connection
     */
    private $database;

    public function __construct(AdsClient $client, Connection $database)
    {
        $this->client = $client;
        $this->database = $database;
    }

    public function sync(): void
    {
        try {
            do {
                $blocks = $this->client->getBlocks();
                $this->parseBlocks($blocks->getBlocks());
            } while ($blocks instanceof GetBlocksResponse);
        } catch (CommandException $ex) {
        }
    }


    private function parseBlocks(array $blocks)
    {
        foreach ($blocks as $blockId) {
            $block = $this->client->getBlock($blockId);
            if ($block instanceof GetBlockResponse) {
                $messageList = $this->client->getPackageList($blockId);
                if ($messageList instanceof GetPackageListResponse) {
                }
            }
        }
    }
}

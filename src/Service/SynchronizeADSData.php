<?php


namespace App\Service;

use Adshares\Ads\AdsClient;
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

    public function __construct(?AdsClient $client, Connection $database)
    {
        $this->client = $client;
        $this->database = $database;
    }

    public function sync()
    {

    }
}
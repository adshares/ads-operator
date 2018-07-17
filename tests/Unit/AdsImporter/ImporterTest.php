<?php


namespace Adshares\AdsOperator\Tests\Unit\AdsImporter;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Command\CommandInterface;
use Adshares\Ads\Response\GetMeResponse;
use Adshares\Ads\Response\GetPackageListResponse;
use Adshares\Ads\Response\GetPackageResponse;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Node;
use Adshares\Ads\Exception\CommandException;
use Adshares\Ads\Response\GetAccountsResponse;
use Adshares\Ads\Response\GetBlockResponse;
use Adshares\AdsOperator\AdsImporter\Database\DatabaseMigrationInterface;
use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\AdsImporter\Importer;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Package;
use Adshares\AdsOperator\Document\Transaction;
use Adshares\AdsOperator\Tests\Unit\PrivateMethodTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class ImporterTest extends TestCase
{
    use PrivateMethodTrait;

    const BLOCK_SEQ_TIME = 32;
    const GENESIS_TIME = 1531733300;
    const PREVIOUS_BLOCK = 1531733396;
    /**
     *
     * `getBlock`: block with 4 nodes
     * `getAccounts`: 4 accounts
     *
     */
    private $adsClient;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $accounts = [new Account(), new Account(), new Account()];
        $block = new Block(1, [new Node(1), new Node(2), new Node(3), new Node(4)]);

        $accountsResponse = $this->createMock(GetAccountsResponse::class);
        $accountsResponse
            ->method('getAccounts')
            ->willReturn($accounts);

        $blockResponse = $this->createMock(GetBlockResponse::class);
        $blockResponse
            ->method('getBlock')
            ->willReturn($block);

        $date = new \DateTime();
        $date->setTimestamp(self::PREVIOUS_BLOCK);

        $getMeResponse = $this->createMock(GetMeResponse::class);
        $getMeResponse
            ->method('getPreviousBlockTime')
            ->willReturn($date);

        $this->adsClient = $this->createMock(AdsClient::class);

        $this->adsClient
            ->method('getAccounts')
            ->willReturn($accountsResponse);

        $this->adsClient
            ->method('getBlock')
            ->willReturn($blockResponse);

        $this->adsClient
            ->method('getMe')
            ->willReturn($getMeResponse);
    }

    public function testStartTimeWhenBlockCollectionIsNotEmpty(): void
    {
        $newestBlockTime = time() - 3600;
        $nextBlockTime = $newestBlockTime + self::BLOCK_SEQ_TIME;
        $genesisTime = time() - 3600*24*30;

        $database = $this->createMock(DatabaseMigrationInterface::class);
        $database
            ->expects($this->once())
            ->method('getNewestBlockTime')
            ->willReturn($newestBlockTime);

        $importer = new Importer($this->adsClient, $database, new NullLogger(), $genesisTime, self::BLOCK_SEQ_TIME);

        $result = $this->invokeMethod($importer, 'getStartTime');
        $this->assertEquals($nextBlockTime, $result);
    }

    public function testStartTimeWhenBlockCollectionIsEmpty(): void
    {
        $newestBlockTime = null;
        $genesisTime = time() - 3600*24*30;

        $database = $this->createMock(DatabaseMigrationInterface::class);
        $database
            ->expects($this->once())
            ->method('getNewestBlockTime')
            ->willReturn($newestBlockTime);

        $importer = new Importer($this->adsClient, $database, new NullLogger(), $genesisTime, self::BLOCK_SEQ_TIME);

        $result = $this->invokeMethod($importer, 'getStartTime');
        $this->assertEquals($genesisTime, $result);
    }

    public function testUpdateNodesWhenGetBlockCannotBeProceed(): void
    {
        $this->expectException(AdsClientException::class);

        $adsClient = $this->adsClient;
        $adsClient
            ->expects($this->once())
            ->method('getBlock')
            ->will($this->throwException(new CommandException($this->createMock(CommandInterface::class))));

        $database = $this->createMock(DatabaseMigrationInterface::class);

        $importer = new Importer($this->adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);
        $this->invokeMethod($importer, 'updateNodes');
    }

    public function testUpdateNodesWhereBlockReturnsNodes(): void
    {
        $database = $this->createMock(DatabaseMigrationInterface::class);

        $importer = new Importer($this->adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);

        $this->invokeMethod($importer, 'updateNodes');
        $this->assertEquals(4, $importer->getResult()->nodes);
    }

    public function testUpdateAccountsWhenClientReturnsAccounts(): void
    {
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $database
            ->expects($this->exactly(3))
            ->method('addOrUpdateAccount')
            ->willReturn(null);

        $node = $this->createMock(Node::class);
        $node
            ->expects($this->once())
            ->method('getId')
            ->willReturn(12);

        $importer = new Importer($this->adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);

        $this->invokeMethod($importer, 'updateAccounts', [$node]);
        $this->assertEquals(3, $importer->getResult()->accounts);
    }

    public function testAddTransactionFromPackageWhenPackageExistsButTransactionsDoNotExist()
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $packageResponse = $this->createMock(GetPackageResponse::class);

        $packageResponse
            ->method('getTransactions')
            ->willReturn([]);

        $adsClient
            ->method('getPackage')
            ->willReturn($packageResponse)
        ;
        $importer = new Importer($adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);

        $result = $this->invokeMethod(
            $importer,
            'addTransactionsFromPackage',
            [new Package('1', 1, 1), new Block(1)]
        );
        $this->assertEquals(0, $result);
    }

    public function testAddTransactionFromPackageWhenPackageExistsAndTransactionsExist()
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $packageResponse = $this->createMock(GetPackageResponse::class);

        $packageResponse
            ->method('getTransactions')
            ->willReturn([new Transaction(), new Transaction()]);

        $adsClient
            ->method('getPackage')
            ->willReturn($packageResponse)
        ;

        $importer = new Importer($adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);

        $result = $this->invokeMethod(
            $importer,
            'addTransactionsFromPackage',
            [new Package('1', 1, 1), new Block(1)]
        );
        $this->assertEquals(2, $result);

        $this->invokeMethod(
            $importer,
            'addTransactionsFromPackage',
            [new Package('1', 1, 1), new Block(1)]
        );
        $this->assertEquals(4, $importer->getResult()->transactions);
    }


    public function testAddPackagesFromBlockWhenPackagesAreEmpty(): void
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $packageListResponse = $this->createMock(GetPackageListResponse::class);

        $packageListResponse
            ->method('getPackages')
            ->willReturn([]);

        $adsClient
            ->method('getPackageList')
            ->willReturn($packageListResponse);

        $importer = new Importer(
            $adsClient,
            $database,
            new NullLogger(),
            time(),
            self::BLOCK_SEQ_TIME
        );

        $result = $this->invokeMethod($importer, 'addPackagesFromBlock', [new Block(1)]);
        $this->assertEquals(0, $result);
    }

    public function testAddPackagesFromBlockWhenPackagesExist(): void
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $packageListResponse = $this->createMock(GetPackageListResponse::class);

        $packageListResponse
            ->method('getPackages')
            ->willReturn([new Package('1', 1, 1), new Package('2', 2, 2)]);

        $adsClient
            ->method('getPackageList')
            ->willReturn($packageListResponse);

        $importer = new Importer(
            $adsClient,
            $database,
            new NullLogger(),
            time(),
            self::BLOCK_SEQ_TIME
        );

        $this->invokeMethod($importer, 'addPackagesFromBlock', [new Block(1)]);
        $this->assertEquals(2, $importer->getResult()->packages);
    }

    public function testImport4Blocks(): void
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $packageListResponse = $this->createMock(GetPackageListResponse::class);

        $packageListResponse
            ->method('getPackages')
            ->willReturn([new Package('1', 1, 1), new Package('2', 2, 2)]);

        $adsClient
            ->method('getPackageList')
            ->willReturn($packageListResponse);

        $importer = new Importer(
            $adsClient,
            $database,
            new NullLogger(),
            self::GENESIS_TIME,
            self::BLOCK_SEQ_TIME
        );

        $importer->import();
        $this->assertEquals(4, $importer->getResult()->blocks);
    }
}

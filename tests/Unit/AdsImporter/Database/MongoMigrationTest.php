<?php

namespace Adshares\AdsOperator\Tests\Unit\AdsImporter\Database;

use Adshares\Ads\Entity\Transaction\SendManyTransactionWire;
use Adshares\AdsOperator\AdsImporter\Database\MongoMigration;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Transaction\EmptyTransaction;
use Adshares\AdsOperator\Document\Transaction\SendManyTransaction;
use Adshares\AdsOperator\Document\Transaction\SendOneTransaction;
use Doctrine\MongoDB\Collection;
use Doctrine\MongoDB\Connection;
use Doctrine\MongoDB\Cursor;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Database;
use PHPUnit\Framework\TestCase;

class MongoMigrationTest extends TestCase
{
    private $connection;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $database = $this->createMock(Database::class);
        $collection = $this->createMock(Collection::class);

        $database
            ->method('createCollection')
            ->willReturn($collection);

        $connection = $this->createMock(Connection::class);
        $connection
            ->method('selectDatabase')
            ->willReturn($database);

        $this->connection = $connection;
    }


    public function testAddMessage(): void
    {
        $message = $this->createMock(Message::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addMessage($message);
        $this->assertTrue(true);
    }

    public function testAddBlock(): void
    {
        $block = $this->createMock(Block::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addBlock($block);
        $this->assertTrue(true);
    }

    public function testAddTransaction():void
    {
        $transaction = $this->createMock(EmptyTransaction::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addTransaction($transaction);
        $this->assertTrue(true);
    }

    public function testAddSendOneTransactionWhenSenderAndTargetAreDifferent(): void
    {
        $transaction = $this->createMock(SendOneTransaction::class);
        $transaction
            ->expects($this->exactly(2))
            ->method('getSenderAddress')
            ->willReturn('0001-00000000-9B6F');

        $transaction
            ->expects($this->exactly(2))
            ->method('getTargetAddress')
            ->willReturn('0001-00000000-1234');

        $mongoMigration = new MongoMigration($this->prepareConnectionForOneAndManyTransactions());
        $mongoMigration->addTransaction($transaction);
        $this->assertTrue(true);
    }

    public function testAddSendOneTransactionWhenSenderAndTargetAreTheSame(): void
    {
        $transaction = $this->createMock(SendOneTransaction::class);
        $transaction
            ->expects($this->exactly(2))
            ->method('getSenderAddress')
            ->willReturn('0001-00000000-9B6F');

        $transaction
            ->expects($this->exactly(1))
            ->method('getTargetAddress')
            ->willReturn('0001-00000000-9B6F');

        $mongoMigration = new MongoMigration($this->prepareConnectionForOneAndManyTransactions());
        $mongoMigration->addTransaction($transaction);
        $this->assertTrue(true);
    }

    public function testAddSendManyTransaction()
    {
        $transaction = $this->createMock(SendManyTransaction::class);
        $wires = $this->createMock(SendManyTransactionWire::class);

        $wires
            ->expects($this->once())
            ->method('getTargetAddress')
            ->willReturn('0001-00000000-1234');

        $transaction
            ->expects($this->exactly(1))
            ->method('getSenderAddress')
            ->willReturn('0001-00000000-9B6F');

        $transaction
            ->expects($this->exactly(1))
            ->method('getWires')
            ->willReturn([$wires]);

        $mongoMigration = new MongoMigration($this->prepareConnectionForOneAndManyTransactions());
        $mongoMigration->addTransaction($transaction);
        $this->assertTrue(true);
    }

    public function testAddOrUpdateNode():void
    {
        $node = $this->createMock(Node::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addOrUpdateNode($node);
        $this->assertTrue(true);
    }

    public function testAddOrUpdateAccount():void
    {
        $account = $this->createMock(Account::class);
        $node = $this->createMock(Node::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addOrUpdateAccount($account, $node);
        $this->assertTrue(true);
    }

    public function testNewestBlockTimeWhenAtLeastOneBlockExists(): void
    {
        $time = time();
        $database = $this->createMock(Database::class);
        $cursor = $this->createMock(Cursor::class);
        $collection = $this->createMock(Collection::class);

        $cursor
            ->method('sort')
            ->willReturn($cursor);

        $cursor
            ->method('limit')
            ->willReturn($cursor);

        $cursor
            ->method('current')
            ->willReturn(['time' => (object) ['sec' => $time]]);

        $collection
            ->expects($this->once())
            ->method('find')
            ->willReturn($cursor);

        $database
            ->method('createCollection')
            ->willReturn($collection);

        $database
            ->method('selectCollection')
            ->willReturn($collection);

        $connection = $this->createMock(Connection::class);
        $connection
            ->method('selectDatabase')
            ->willReturn($database);


        $mongoMigration = new MongoMigration($connection);
        $result = $mongoMigration->getNewestBlockTime();

        $this->assertEquals($time, $result);
    }

    public function testNewestBlockTimeWhenBlockDoesNotExist(): void
    {
        $database = $this->createMock(Database::class);
        $cursor = $this->createMock(Cursor::class);
        $collection = $this->createMock(Collection::class);

        $cursor
            ->method('sort')
            ->willReturn($cursor);

        $cursor
            ->method('limit')
            ->willReturn($cursor);

        $cursor
            ->method('current')
            ->willReturn(null);

        $collection
            ->expects($this->once())
            ->method('find')
            ->willReturn($cursor);

        $database
            ->method('createCollection')
            ->willReturn($collection);

        $database
            ->method('selectCollection')
            ->willReturn($collection);

        $connection = $this->createMock(Connection::class);
        $connection
            ->method('selectDatabase')
            ->willReturn($database);


        $mongoMigration = new MongoMigration($connection);
        $result = $mongoMigration->getNewestBlockTime();

        $this->assertNull($result);
    }

    private function prepareConnectionForOneAndManyTransactions()
    {
        $database = $this->createMock(Database::class);
        $collection = $this->createMock(Collection::class);
        $collection
            ->expects($this->once())
            ->method('batchInsert')
            ->willReturn(null);

        $database
            ->method('createCollection')
            ->willReturn($collection);

        $connection = $this->createMock(Connection::class);
        $connection
            ->method('selectDatabase')
            ->willReturn($database);

        return $connection;
    }
}

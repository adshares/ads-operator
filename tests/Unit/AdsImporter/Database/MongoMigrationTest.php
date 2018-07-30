<?php

namespace Adshares\AdsOperator\Tests\Unit\AdsImporter\Database;

use Adshares\Ads\Entity\Transaction\AbstractTransaction;
use Adshares\Ads\Entity\Transaction\SendManyTransactionWire;
use Adshares\AdsOperator\AdsImporter\Database\MongoMigration;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Transaction\BroadcastTransaction;
use Adshares\AdsOperator\Document\Transaction\ConnectionTransaction;
use Adshares\AdsOperator\Document\Transaction\EmptyTransaction;
use Adshares\AdsOperator\Document\Transaction\KeyTransaction;
use Adshares\AdsOperator\Document\Transaction\LogAccountTransaction;
use Adshares\AdsOperator\Document\Transaction\NetworkTransaction;
use Adshares\AdsOperator\Document\Transaction\SendManyTransaction;
use Adshares\AdsOperator\Document\Transaction\SendOneTransaction;
use Adshares\AdsOperator\Document\Transaction\StatusTransaction;
use Doctrine\MongoDB\Collection;
use Doctrine\MongoDB\Connection;
use Doctrine\MongoDB\Cursor;
use MongoDB\Database;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class MongoMigrationTest extends TestCase
{
    private $connection;

    /**
     * @var MockObject
     */
    private $collection;

    /**
     * @var MockObject
     */
    private $database;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp()
    {
        parent::setUp();

        $database = $this->createMock(Database::class);
        $collection = $this->createMock(Collection::class);

        $database
            ->method('selectCollection')
            ->willReturn($collection);

        $connection = $this->createMock(Connection::class);
        $connection
            ->method('selectDatabase')
            ->willReturn($database);

        $this->collection = $collection;
        $this->database = $database;
        $this->connection = $connection;
    }

    public function testAddMessage(): void
    {
        $this->prepareConnectionMockWithMethod('insert');
        $message = $this->createMock(Message::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addMessage($message);
    }

    public function testAddBlock(): void
    {
        $this->prepareConnectionMockWithMethod('insert');
        $block = $this->createMock(Block::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addBlock($block);
    }

    public function testAddBlocWhenDuplicated(): void
    {
        $this->prepareConnectionMockWithMethod('insert', $this->throwException(new \MongoDuplicateKeyException()));
        $block = $this->createMock(Block::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addBlock($block);
    }


    private function prepareConnectionMockWithMethod(string $method, $return = null)
    {
        if (!$return) {
            $return = $this->createMock(Stub::class);
        }

        $this->collection
            ->expects($this->once())
            ->method($method)
            ->will($return);

        $this->database
            ->method('selectCollection')
            ->willReturn($this->collection);

        $this->connection
            ->method('selectDatabase')
            ->willReturn($this->database);
    }

    public function testAllTypesOfTransactions():void
    {
        $transactions = [
            BroadcastTransaction::class,
            ConnectionTransaction::class,
            EmptyTransaction::class,
            KeyTransaction::class,
            LogAccountTransaction::class,
            NetworkTransaction::class,
            StatusTransaction::class,
            SendOneTransaction::class,
            SendManyTransaction::class,
        ];

        /** @var AbstractTransaction $transaction */
        foreach ($transactions as $class) {
            $transaction = $class::createFromRawData([
                'time' => time(),
                'senderAddress' => '1234',
                'id' => '12312',
                'target_address' => '1222',
                'wires' => [
                    [
                        'amount' => 123,
                        'target_address' => '123123',
                        'target_node' => 1,
                        'target_user' => 1,
                    ],
                ]
            ]);
            $mongoMigration = new MongoMigration($this->connection);
            $mongoMigration->addTransaction($transaction);
            $this->assertTrue(true);
        }
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
        $this->prepareConnectionMockWithMethod('insert');
        $node = $this->createMock(Node::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addOrUpdateNode($node);
    }

    public function testAddOrUpdateNodeWhenMongoExceptionIsThrown():void
    {
        $this->prepareConnectionMockWithMethod('insert', $this->throwException(new \MongoDuplicateKeyException()));
        $this->prepareConnectionMockWithMethod('update');
        $node = $this->createMock(Node::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addOrUpdateNode($node);
    }


    public function testAddOrUpdateAccount():void
    {
        $this->prepareConnectionMockWithMethod('update');
        $account = $this->createMock(Account::class);
        $node = $this->createMock(Node::class);

        $mongoMigration = new MongoMigration($this->connection);
        $mongoMigration->addOrUpdateAccount($account, $node);
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
            ->method('selectCollection')
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
            ->method('selectCollection')
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
            ->method('selectCollection')
            ->willReturn($collection);

        $connection = $this->createMock(Connection::class);
        $connection
            ->method('selectDatabase')
            ->willReturn($database);

        return $connection;
    }
}

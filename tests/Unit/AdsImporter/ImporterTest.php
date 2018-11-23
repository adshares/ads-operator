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

namespace Adshares\AdsOperator\Tests\Unit\AdsImporter;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Command\CommandInterface;
use Adshares\Ads\Command\GetMessageCommand;
use Adshares\Ads\Command\GetMessageIdsCommand;
use Adshares\Ads\Driver\CommandError;
use Adshares\Ads\Response\GetAccountResponse;
use Adshares\Ads\Response\GetMessageIdsResponse;
use Adshares\Ads\Response\GetMessageResponse;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\AdsOperator\Document\Node;
use Adshares\Ads\Exception\CommandException;
use Adshares\Ads\Response\GetAccountsResponse;
use Adshares\Ads\Response\GetBlockResponse;
use Adshares\AdsOperator\AdsImporter\Database\DatabaseMigrationInterface;
use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\AdsImporter\Importer;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Tests\Unit\PrivateMethodTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
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

        $accounts = [new Account('01'), new Account('02'), new Account('03')];
        $block = new Block('1', [new Node('1'), new Node('2'), new Node('3'), new Node('4'), new Node('0000')], 4);

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

        $getMeResponse = $this->createMock(GetAccountResponse::class);
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
        $nextBlockTime = $newestBlockTime;
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
        $database
            ->expects($this->never())
            ->method('addOrUpdateNode');

        $importer = new Importer($this->adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);
        $this->invokeMethod($importer, 'updateNodes');
    }

    public function testUpdateNodesWhenGetBlockCannotBeProceedAndBlockIsUnavailable()
    {
        $adsClient = $this->adsClient;
        $adsClient
            ->expects($this->once())
            ->method('getBlock')
            ->will(
                $this->throwException(
                    new CommandException(
                        $this->createMock(CommandInterface::class),
                        '',
                        CommandError::GET_BLOCK_INFO_UNAVAILABLE
                    )
                )
            );

        $database = $this->createMock(DatabaseMigrationInterface::class);
        $database
            ->expects($this->never())
            ->method('addOrUpdateNode');


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

    public function testUpdateNodesVersion(): void
    {
        $version = '0.0.1';

        $database = $this->createMock(DatabaseMigrationInterface::class);
        $database
            ->expects($this->exactly(4))
            ->method('getNodeVersion')
            ->willReturn($version);

        $database
            ->expects($this->exactly(4))
            ->method('addOrUpdateNode')
            ->with($this->callback(
                function (Node $node) use ($version) {
                    return $node->getVersion() === $version;
                }
            ));

        $importer = new Importer($this->adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);
        $this->invokeMethod($importer, 'updateNodes');
    }

    public function testUpdateNodesTransactionCount(): void
    {
        $count = 123;

        $database = $this->createMock(DatabaseMigrationInterface::class);
        $database
            ->expects($this->exactly(4))
            ->method('getNodeTransactionCount')
            ->willReturn($count);

        $database
            ->expects($this->exactly(4))
            ->method('addOrUpdateNode')
            ->with($this->callback(
                function (Node $node) use ($count) {
                    return $node->getTransactionCount() === $count;
                }
            ));

        $importer = new Importer($this->adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);
        $this->invokeMethod($importer, 'updateNodes');
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

    public function testUpdateAccountTransactionCount(): void
    {
        $count = 123;

        $database = $this->createMock(DatabaseMigrationInterface::class);
        $database
            ->expects($this->exactly(3))
            ->method('getAccountTransactionCount')
            ->willReturn($count);

        $database
            ->expects($this->exactly(3))
            ->method('addOrUpdateAccount')
            ->with($this->callback(
                function (Account $account) use ($count) {
                    return $account->getTransactionCount() === $count;
                }
            ));

        $node = $this->createMock(Node::class);
        $node
            ->expects($this->once())
            ->method('getId')
            ->willReturn(12);

        $importer = new Importer($this->adsClient, $database, new NullLogger(), time(), self::BLOCK_SEQ_TIME);
        $this->invokeMethod($importer, 'updateAccounts', [$node]);
    }

    public function testAddMessagesForBlockWhenTwoMessagesAndSixTransactionsExist()
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $getMessageIdsResponse = $this->createMock(GetMessageIdsResponse::class);
        $messageResponse = $this->createMock(GetMessageResponse::class);
        $transaction = $this->createMock(ArrayableInterface::class);

        $getMessageIdsResponse
            ->method('getMessageIds')
            ->willReturn(['1', '2']);

        $adsClient
            ->method('getMessageIds')
            ->willReturn($getMessageIdsResponse);

        $message = new Message(3);

        $messageResponse
            ->method('getMessage')
            ->willReturn($message);
        $messageResponse
            ->method('getTransactions')
            ->willReturn([$transaction, $transaction, $transaction]);

        $adsClient
            ->method('getMessage')
            ->willReturn($messageResponse);

        $importer = new Importer(
            $adsClient,
            $database,
            new NullLogger(),
            time(),
            self::BLOCK_SEQ_TIME
        );

        $result = $this->invokeMethod($importer, 'addMessagesFromBlock', [new Block('1')]);
        $this->assertEquals(6, $result); // 2 messages x 3 transactions
    }

    public function testAddMessagesForBlockWhenMessageExistsButTransactionsDoNotExist()
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $messageResponse = $this->createMock(GetMessageResponse::class);

        $getMessageIdsResponse = $this->createMock(GetMessageIdsResponse::class);
        $getMessageIdsResponse
            ->method('getMessageIds')
            ->willReturn(['1', '2']);

        $adsClient
            ->method('getMessageIds')
            ->willReturn($getMessageIdsResponse);

        $message = new Message(0);
        $messageResponse
            ->method('getMessage')
            ->willReturn($message);
        $messageResponse
            ->method('getTransactions')
            ->willReturn([]);

        $adsClient
            ->method('getMessage')
            ->willReturn($messageResponse);

        $importer = new Importer(
            $adsClient,
            $database,
            new NullLogger(),
            time(),
            self::BLOCK_SEQ_TIME
        );

        $result = $this->invokeMethod($importer, 'addMessagesFromBlock', [new Block('1')]);
        $this->assertEquals(0, $result);
    }

    public function testAddMessagesForBlockWhenMessagesDoNotExist()
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $getMessageIdsResponse = $this->createMock(GetMessageIdsResponse::class);

        $getMessageIdsResponse
            ->method('getMessageIds')
            ->willReturn(['1', '2']);

        $adsClient
            ->method('getMessageIds')
            ->willReturn($getMessageIdsResponse);

        $adsClient
            ->method('getMessage')
            ->will($this->throwException($this->createMock(CommandException::class)));

        $importer = new Importer(
            $adsClient,
            $database,
            new NullLogger(),
            time(),
            self::BLOCK_SEQ_TIME
        );

        $result = $this->invokeMethod($importer, 'addMessagesFromBlock', [new Block('1')]);
        $this->assertEquals(0, $result);
    }

    public function testAddMessageFromBlockWhenAdsClientThrowsAnException()
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error');

        $adsClient
            ->method('getMessageIds')
            ->will($this->throwException(new CommandException(new GetMessageIdsCommand('123'))));

        $importer = new Importer(
            $adsClient,
            $database,
            $logger,
            time(),
            self::BLOCK_SEQ_TIME
        );

        $result = $this->invokeMethod($importer, 'addMessagesFromBlock', [new Block('1')]);
        $this->assertEquals(0, $result);
    }

    public function testGetMessageResponseWhenAdsClientReturnsMessage()
    {
        $messageId = '0001:0000003E';
        $data = [
            'node' => 1,
            'message_id' => '"0001:00000001"',
        ];
        $messageResponse = new GetMessageResponse($data);

        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);

        $adsClient
            ->method('getMessage')
            ->willReturn($messageResponse);

        $importer = new Importer(
            $adsClient,
            $database,
            new NullLogger(),
            time(),
            self::BLOCK_SEQ_TIME
        );

        $result = $this->invokeMethod($importer, 'getMessageResponse', [$messageId, new Block('1')]);
        $this->assertEquals($messageResponse, $result);
    }

    public function testGetMessageResponseWhenAdsClientThrowsAnException()
    {
        $messageId = '0001:0000003E';
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error');

        $adsClient
            ->method('getMessage')
            ->will($this->throwException(new CommandException(new GetMessageCommand($messageId))));

        $importer = new Importer(
            $adsClient,
            $database,
            $logger,
            time(),
            self::BLOCK_SEQ_TIME
        );

        $this->invokeMethod($importer, 'getMessageResponse', [$messageId, new Block('1')]);
    }

    public function testAddTransactionsFromMessage()
    {
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $database
            ->expects($this->exactly(4))
            ->method('addTransaction')
            ->willReturn(null);

        $importer = new Importer(
            $this->adsClient,
            $database,
            new NullLogger(),
            time(),
            self::BLOCK_SEQ_TIME
        );

        $message = new Message(0);

        $transactions = [
            $this->createMock(ArrayableInterface::class),
            $this->createMock(ArrayableInterface::class),
            $this->createMock(ArrayableInterface::class),
            $this->createMock(ArrayableInterface::class),
        ];

        $result = $this->invokeMethod($importer, 'addTransactionsFromMessage', [$message, $transactions]);
        $this->assertNull($result);
    }

    public function testImport(): void
    {
        $adsClient = $this->adsClient;
        $database = $this->createMock(DatabaseMigrationInterface::class);
        $messageIdsResponse = $this->createMock(GetMessageIdsResponse::class);
        $messageResponse = $this->createMock(GetMessageResponse::class);
        $transaction = $this->createMock(ArrayableInterface::class);

        $messageIdsResponse
            ->method('getMessageIds')
            ->willReturn(['0001:0000003E', '0001:0000003D']);

        $adsClient
            ->method('getMessageIds')
            ->willReturn($messageIdsResponse);

        $messageIdsResponse
            ->method('getMessageIds')
            ->willReturn(['1', '2']);

        $message = new Message(2);

        $messageResponse
            ->method('getMessage')
            ->willReturn($message);
        $messageResponse
            ->method('getTransactions')
            ->willReturn([$transaction, $transaction, $transaction]);

        $adsClient
            ->method('getMessage')
            ->willReturn($messageResponse);

        $importer = new Importer(
            $adsClient,
            $database,
            new NullLogger(),
            self::GENESIS_TIME,
            self::BLOCK_SEQ_TIME
        );

        $importer->import();

        $this->assertEquals(24, $importer->getResult()->transactions); // 2 messages * 3 transactions * 4 blocks
        $this->assertEquals(4, $importer->getResult()->blocks);
        $this->assertEquals(8, $importer->getResult()->messages);
        $this->assertEquals(4, $importer->getResult()->nodes);
        $this->assertEquals(12, $importer->getResult()->accounts); // 4 nodes * 3 accounts
    }
}

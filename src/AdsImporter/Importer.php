<?php

/**
 * Copyright (c) 2018-2022 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types=1);

namespace Adshares\AdsOperator\AdsImporter;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CommandError;
use Adshares\Ads\Exception\CommandException;
use Adshares\Ads\Response\GetBlockResponse;
use Adshares\Ads\Response\GetMessageResponse;
use Adshares\AdsOperator\AdsImporter\Database\DatabaseMigrationInterface;
use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\AdsImporter\Exception\AdsImporterException;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Info;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Snapshot;
use Adshares\AdsOperator\Document\SnapshotAccount;
use Adshares\AdsOperator\Document\SnapshotNode;
use Adshares\AdsOperator\Document\Transaction\ConnectionTransaction;
use Adshares\AdsOperator\Document\Transaction\DividendTransaction;
use Adshares\AdsOperator\Document\Transaction\NetworkTransaction;
use Adshares\AdsOperator\Document\Transaction\SendManyTransaction;
use Adshares\AdsOperator\Document\Transaction\SendOneTransaction;
use Adshares\AdsOperator\Helper\NumericalTransformation;
use DateTime;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Imports network's data using ADS Client.
 *
 * @package Adshares\AdsOperator\AdsImporter
 */
class Importer
{
    public const DIV_BLOCKS = 2048; // number of blocks for dividend update (dividend period 12 days)
    public const ACCOUNT_INACTIVE_AGE = 365 * 24 * 3600; // account is considered inactive after one year
    public const ACCOUNT_DORMANT_AGE = 2 * 365 * 24 * 3600; // account is considered dormant after two years
    public const TXS_DIV_FEE = 2_0000_000;  //(0x100000)  // dividend fee collected every DIV_BLOCKS blocks ($0.1 / year)
    public const CLICKS = 1e11; // 100_000_000_000 clicks in 1 ADS

    private AdsClient $client;
    private DatabaseMigrationInterface $databaseMigration;
    private LoggerInterface $logger;
    private int $amountPrecision;
    private int $totalSupply;
    private int $genesisTime;
    private int $blockLength;
    private array $nonCirculatingAccounts;
    private ImporterResult $importerResult;

    /**
     * Importer constructor.
     * @param AdsClient $client
     * @param DatabaseMigrationInterface $databaseMigration
     * @param LoggerInterface $logger
     * @param int $totalSupply
     * @param int $amountPrecision
     * @param int $genesisTime
     * @param int $blockLength
     * @param string $nonCirculatingAccounts
     */
    public function __construct(
        AdsClient $client,
        DatabaseMigrationInterface $databaseMigration,
        LoggerInterface $logger,
        int $totalSupply,
        int $amountPrecision,
        int $genesisTime,
        int $blockLength,
        string $nonCirculatingAccounts
    ) {
        $this->client = $client;
        $this->databaseMigration = $databaseMigration;
        $this->logger = $logger;
        $this->totalSupply = $totalSupply;
        $this->amountPrecision = $amountPrecision;
        $this->genesisTime = $genesisTime;
        $this->blockLength = $blockLength;
        $this->nonCirculatingAccounts = array_filter(explode(',', $nonCirculatingAccounts));
        $this->importerResult = new ImporterResult();
    }

    private ?array $genesisData = null;

    public function loadGenesis($root)
    {
        $this->genesisData = json_decode(file_get_contents($root . '/genesis.json'), true);
    }

    private function getStartingBalance($accountId)
    {
        if ($this->genesisData === null) {
            throw new \RuntimeException("Genesis not loaded");
        }
        [$node, $user, $checksum] = explode('-', $accountId);
        $node = hexdec($node) - 1;
        $user = hexdec($user);

        return (int)(($this->genesisData['nodes'][$node]['accounts'][$user]['balance']
                ?? (self::TXS_DIV_FEE / self::CLICKS)) * (self::CLICKS));
    }

    private function updateMissingDividend(Account $account, Block $block)
    {
        $r = $this->addMissingDividend(
            $account->getRemoteChange()->getTimestamp(),
            $account->getLocalChange()->getTimestamp(),
            $account->getBalance(),
            $block->getTime()->getTimestamp(),
            $block->getDividendBalance()
        );
        $account->setBalance($r['balance']);
        $account->setRemoteChange(new DateTime('@' . $r['remoteChange']));
        if ($r['dividend'] != 0) {
            $lastDividendBlockTime = $block->getTime()->getTimestamp() -
                ($block->getTime()->getTimestamp() % ($this->blockLength * self::DIV_BLOCKS));
            $this->addDividendTransaction(
                $account->getAddress(),
                strtoupper(dechex($lastDividendBlockTime)),
                $r['dividend']
            );
        }
    }

    private function addDividendTransaction($targetAccount, $blockId, $div)
    {
        $tx = new DividendTransaction();
        $tx->fillWithRawData(
            [
                '_id' => 'dividend:' . $targetAccount . ':' . $blockId,
                'size' => 0,
                'type' => 'dividend',
                'nodeId' => 0,
                'blockId' => $blockId,
                'messageId' => 0,
                'amount' => $div,
                'targetAddress' => $targetAccount
            ]
        );
        $this->databaseMigration->addTransaction($tx);
    }

    private function addMissingDividend(
        $accountRemoteChange,
        $accountLocalChange,
        $accountBalance,
        $blockTime,
        $blockDividendBalance
    ): array {
        $lastDividendBlockTime = $blockTime - ($blockTime % ($this->blockLength * self::DIV_BLOCKS));
        $div = 0;
        if ($accountRemoteChange < $lastDividendBlockTime) {
            if (
                $accountLocalChange - $this->blockLength * self::DIV_BLOCKS <
                $lastDividendBlockTime - self::ACCOUNT_DORMANT_AGE
            ) {
                $div = -(int)((int)$accountBalance / 1000);
            } elseif (
                $accountLocalChange - $this->blockLength * self::DIV_BLOCKS <
                $lastDividendBlockTime - self::ACCOUNT_INACTIVE_AGE
            ) {
                $div = 0;
            } else {
                $div = (int)((((int)$accountBalance) >> 16) * $blockDividendBalance);
            }

            $div -= self::TXS_DIV_FEE;
            if ($div < -$accountBalance) {
                $div = -$accountBalance;
            }
            $accountRemoteChange = $lastDividendBlockTime;
        }
        return [
            'balance' => max(0, $accountBalance + $div),
            'remoteChange' => $accountRemoteChange,
            'dividend' => $div
        ];
    }

    private static $TX_CLASS_MAP = [
        'broadcast' => '\Adshares\AdsOperator\Document\Transaction\BroadcastTransaction',
        'connection' => '\Adshares\AdsOperator\Document\Transaction\ConnectionTransaction',
        'create_account' => '\Adshares\AdsOperator\Document\Transaction\NetworkTransaction',
        'account_created' => '\Adshares\AdsOperator\Document\Transaction\NetworkTransaction',
        'retrieve_funds' => '\Adshares\AdsOperator\Document\Transaction\NetworkTransaction',
        'empty' => '\Adshares\AdsOperator\Document\Transaction\EmptyTransaction',
        'change_account_key' => '\Adshares\AdsOperator\Document\Transaction\KeyTransaction',
        'log_account' => '\Adshares\AdsOperator\Document\Transaction\LogAccountTransaction',
        'network' => '\Adshares\AdsOperator\Document\Transaction\NetworkTransaction',
        'send_many' => '\Adshares\AdsOperator\Document\Transaction\SendManyTransaction',
        'send_one' => '\Adshares\AdsOperator\Document\Transaction\SendOneTransaction',
        'set_account_status' => '\Adshares\AdsOperator\Document\Transaction\NetworkTransaction',
        'unset_account_status' => '\Adshares\AdsOperator\Document\Transaction\NetworkTransaction',
        'set_bank_status' => '\Adshares\AdsOperator\Document\Transaction\NetworkTransaction',
        'unset_bank_status' => '\Adshares\AdsOperator\Document\Transaction\NetworkTransaction',
    ];

    public function calculateDividends(): int
    {
        $blockDividends = [];
        $accounts = $this->databaseMigration->getAllAccounts();
        foreach ($accounts as $account) {
            foreach ($account as &$value) {
                if ($value instanceof \MongoDate) {
                    $value = $value->sec;
                }
            }
            unset($value);
            if (stristr($account['_id'], '-00000000-')) {
                // node operator. Not brave enough to calculate this
                continue;
            }

            $transactions = $this->databaseMigration->getAccountTransactions($account['_id']);

            $currentBlock = 1535460864; // genesis

            $accBalance = $this->getStartingBalance($account['_id']);
            $accLastActive = 1535460864;

            if ($account['msid'] == 1) {
                $accLastActive = $account['localChange'];
                $currentBlock = $accLastActive - $accLastActive % $this->blockLength;
            } elseif ($accBalance == self::TXS_DIV_FEE) {
                $transactions->rewind();
                $tx = $transactions->current();
                if ($tx) {
                    $accLastActive = $tx['time']->sec - $tx['time']->sec % $this->blockLength;
                    $currentBlock = $accLastActive - $accLastActive % $this->blockLength;
                }
            }

            $advanceBlockFn = function ($nextBlock) use (
                $account,
                &$blockDividends,
                &$currentBlock,
                &$accBalance,
                &$accLastActive
            ) {
                for ($block = $currentBlock + $this->blockLength; $block <= $nextBlock; $block += $this->blockLength) {
                    $blockHex = strtoupper(dechex($block));

                    if ($block % ($this->blockLength * self::DIV_BLOCKS) == 0) {
                        if (!isset($blockDividends[$block])) {
                            $divBlockData = $this->databaseMigration->getBlock(
                                strtoupper(dechex($block))
                            );
                            $blockDividends[$block] = $divBlockData['dividendBalance'] ?? 0;
                        }
                        $fee = 0;
                        if ($accLastActive - $this->blockLength * self::DIV_BLOCKS / 2 < $block - self::ACCOUNT_DORMANT_AGE) {
                            $div = -(int)((int)$accBalance / 1000);
                        } elseif (
                            $accLastActive - $this->blockLength * self::DIV_BLOCKS / 2 < $block
                            - self::ACCOUNT_INACTIVE_AGE
                        ) {
                            $div = 0;
                        } else {
                            $div = (((int)$accBalance) >> 16) * $blockDividends[$block];
                        }
                        $div -= self::TXS_DIV_FEE;

                        if ($div < -$accBalance) {
                            $div = -$accBalance;
                        } else {
//                            $fee=min($accBalance, self::TXS_DIV_FEE);
                        }

                        if ($div != 0) {
                            $this->addDividendTransaction($account['_id'], $blockHex, $div);
                            echo("$blockHex {$account['_id']} dividend " . $div / self::CLICKS . "!\n");
                        }

                        $accBalance += $div;
                    }
                }
                $currentBlock = $nextBlock;
            };

            $types = [];

            foreach ($transactions as $txData) {
                if ($txData['type'] == 'dividend') {
                    continue;
                }
                foreach ($txData as &$value) {
                    if ($value instanceof \MongoDate) {
                        $value = $value->sec;
                    }
                }
//                    print_r($txData);
//                    exit;
                $class = self::$TX_CLASS_MAP[$txData['type']];
                $tx = new $class();
                if (isset($txData['amount'])) {
                    $txData['amount'] /= self::CLICKS;
                }
                if (isset($txData['senderFee'])) {
                    $txData['senderFee'] /= self::CLICKS;
                }

                if (isset($txData['wires'])) {
                    foreach ($txData['wires'] as &$wire) {
                        $wire['amount'] /= self::CLICKS;
                    }
                    unset($wire);
                }

                $tx->fillWithRawData($txData);


                if ($tx->getSenderAddress() == $account['_id']) {
                    $advanceBlockFn(
                        $tx->getTime()->getTimestamp() - $tx->getTime()->getTimestamp() % $this->blockLength
                    );
                    $accLastActive = $tx->getTime()->getTimestamp();
                }

                $advanceBlockFn(hexdec($tx->getBlockId()));
                $types[$txData['type']] = ($types[$txData['type']] ?? 0) + 1;

                if ($tx->getSenderAddress() == $account['_id']) {
                    $accLastActive = $tx->getTime()->getTimestamp() -
                        $tx->getTime()->getTimestamp() % $this->blockLength + $this->blockLength;
                    $accBalance -= $tx->getSenderFee();
                }

                if ($tx instanceof SendOneTransaction) {
                    if ($tx->getSenderAddress() == $account['_id']) {
                        $accBalance -= $tx->getAmount();
                    }
                    if ($tx->getTargetAddress() == $account['_id']) {
                        $accBalance += $tx->getAmount();
                    }
                } elseif ($tx instanceof SendManyTransaction) {
                    $tmp = 0;
                    foreach ($tx->getWires() as $wire) {
                        if ($tx->getSenderAddress() == $account['_id']) {
                            $accBalance -= $wire->getAmount();
                            $tmp++;
                        }
                        if ($wire->getTargetAddress() == $account['_id']) {
                            $accBalance += $wire->getAmount();
                            $tmp++;
                        }
                    }
                    if ($tmp == 0) {
                        print_r($tx);
                        exit;
                    }
                } elseif ($tx instanceof NetworkTransaction) {
                    if ($tx->getType() == 'account_created') {
                        $accBalance -= $tx->getSenderFee();
                    }
                }
            }

            $advanceBlockFn(hexdec($this->databaseMigration->getLatestBlockId()));

            $blockData = $this->databaseMigration->getBlock(
                strtoupper(dechex($currentBlock))
            );

            $r = $this->addMissingDividend(
                $account['remoteChange'],
                $account['localChange'],
                $account['balance'],
                $currentBlock,
                $blockData['dividendBalance']
            );
            $account['remoteChange'] = $r['remoteChange'];
            $account['balance'] = $r['balance'];

//            if(abs($accBalance - $account['balance']) > 2) {
            echo "Mismatch for account {$account['_id']}; diff = "
                . sprintf("%.11f", ($accBalance - $account['balance']) / self::CLICKS)
                . " ADS\n";
//                print_r($types);
//                exit;
//
        }
        return 1;
    }

    /**
     * @return ImporterResult
     */
    public function import(): ImporterResult
    {
        $getMeResponse = $this->client->getMe();
        $startTime = $this->getStartTime();
        $endTime = (int)$getMeResponse->getPreviousBlockTime()->format('U');
        $blockId = NumericalTransformation::decToHex($startTime);

        do {
            try {
                $blockResponse = $this->client->getBlock($blockId);
                $this->logger->info(sprintf("Processing BLOCK %s", $blockId));

                /** @var Block $block */
                $block = $blockResponse->getBlock();

                if ($block->getMessageCount() > 0) {
                    $blockTransactions = $this->addMessagesFromBlock($block);

                    $block->setTransactionCount($blockTransactions);
                }

                $this->databaseMigration->addBlock($block, $this->blockLength);
                ++$this->importerResult->blocks;
            } catch (CommandException $ex) {
                if ($ex->getCode() !== CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                    $this->addExceptionToLog($ex, sprintf('get_block (%s)', $blockId));
                }
            }

            $startTime += $this->blockLength;
            $blockId = NumericalTransformation::decToHex($startTime);
        } while ($startTime <= $endTime);


        if ($this->importerResult->blocks > 0) {
            try {
                $blockResponse = $this->client->getBlock();
                $this->updateNodes($blockResponse);
                $this->updateInfo($blockResponse);
                $this->updateSnapshot($blockResponse);
            } catch (CommandException $ex) {
                if ($ex->getCode() !== CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                    throw new AdsClientException('Cannot proceed importing data: ' . $ex->getMessage());
                }
            }
        }

        return $this->importerResult;
    }

    public function calculateSnapshots(array $blockIds = null, bool $force = false): array
    {
        if (empty($blockIds)) {
            $blockIds = [$this->getCurrentBlockId()];
        }

        $calculated = [];
        foreach ($blockIds as $blockId) {
            if (!Block::validateId($blockId)) {
                throw new AdsImporterException(sprintf('"%s" is not a valid block ID', $blockId));
            }
            if (!$force && !$this->isDivBlock($blockId)) {
                throw new AdsImporterException(sprintf('"%s" is not a dividend block', $blockId));
            }
            $snapshot = $this->databaseMigration->getSnapshot($blockId);
            if (!$force && null !== $snapshot) {
                continue;
            }
            if (!$force && $blockId !== $this->databaseMigration->getLatestBlockId()) {
                throw new AdsImporterException(sprintf('"%s" is not the latest block', $blockId));
            }
            $this->calculateSnapshot($blockId);
            ++$this->importerResult->snapshots;
            $calculated[] = $blockId;
        }

        return $calculated;
    }

    private function calculateSnapshot(string $blockId): Snapshot
    {
        $this->logger->info(sprintf('Calculating SNAPSHOT %s', $blockId));
        $snapshot = Snapshot::create($blockId);
        $this->databaseMigration->addOrUpdateSnapshot($snapshot);

        foreach ($this->databaseMigration->getNodes() as $node) {
            /** @var SnapshotNode $snapshotNode */
            $snapshotNode = SnapshotNode::createFromRawData($node);
            $snapshotNode->setSnapshotId($snapshot->getId());
            $this->databaseMigration->addOrUpdateSnapshotNode($snapshotNode);
        }

        foreach ($this->databaseMigration->getAccounts() as $account) {
            /** @var SnapshotAccount $snapshotAccount */
            $snapshotAccount = SnapshotAccount::createFromRawData($account);
            $snapshotAccount->setSnapshotId($snapshot->getId());
            $this->databaseMigration->addOrUpdateSnapshotAccount($snapshotAccount);
        }

        return $snapshot;
    }

    private function updateSnapshot(GetBlockResponse $blockResponse): void
    {
        $blockId = $blockResponse->getBlock()->getId();
        if ($this->isDivBlock($blockId)) {
            try {
                $this->calculateSnapshot($blockId);
                ++$this->importerResult->snapshots;
            } catch (AdsImporterException $exception) {
                $this->addExceptionToLog($exception, sprintf('Snapshot (%s)', $blockId));
            }
        }
    }

    /**
     * @return int
     */
    private function getStartTime(): int
    {
        $from = hexdec($this->databaseMigration->getLatestBlockId());

        if ($from) {
            return $from;
        }

        return $this->genesisTime;
    }

    private function updateNodes(GetBlockResponse $blockResponse): void
    {
        $nodes = $blockResponse->getBlock()->getNodes();
        /** @var Block $block */
        $block = $blockResponse->getBlock();

        /** @var Node $node */
        foreach ($nodes as $node) {
            $this->logger->info(sprintf("Processing NODE %s", $node->getId()));
            if ($node->isSpecial()) {
                continue;
            }

            $node->setVersion($this->databaseMigration->getNodeVersion($node->getId()));
            $node->setTransactionCount($this->databaseMigration->getNodeTransactionCount($node->getId()));

            $this->updateAccounts($node, $block);

            $this->databaseMigration->addOrUpdateNode($node);
            ++$this->importerResult->nodes;
        }
    }

    private function updateAccounts(Node $node, Block $block): void
    {
        $accountResponse = $this->client->getAccounts($node->getId());
        $accounts = $accountResponse->getAccounts();

        /** @var Account $account */
        foreach ($accounts as $account) {
            $this->updateMissingDividend($account, $block);
            $this->logger->info(sprintf("Processing ACCOUNT %s", $account->getAddress()));
            $account->setTransactionCount($this->databaseMigration->getAccountTransactionCount($account->getAddress()));
            $this->databaseMigration->addOrUpdateAccount($account, $node);
            ++$this->importerResult->accounts;
        }
    }

    private function addMessagesFromBlock(Block $block): int
    {
        $blockTransactionsCount = 0;

        try {
            $messageIdResponse = $this->client->getMessageIds($block->getId());
            $messageIds = $messageIdResponse->getMessageIds();

            foreach ($messageIds as $messageId) {
                $this->logger->info(sprintf("Processing MESSAGE %s", $messageId));
                $messageResponse = $this->getMessageResponse($messageId, $block);

                if (!$messageResponse) {
                    continue;
                }

                /** @var Message $message */
                $message = $messageResponse->getMessage();
                $transactions = $messageResponse->getTransactions();
                $transactionsCount = count($transactions);

                $message->setTransactionCount($transactionsCount);
                $this->databaseMigration->addMessage($message);
                ++$this->importerResult->messages;

                if ($transactions) {
                    $this->addTransactionsFromMessage($message, $transactions);
                    $blockTransactionsCount += $transactionsCount;
                }
            }
        } catch (CommandException $ex) {
            $this->addExceptionToLog($ex, 'get_message_ids', $block);
        }

        return $blockTransactionsCount;
    }

    /**
     * @param string $messageId
     * @param Block $block
     * @return GetMessageResponse|null
     */
    private function getMessageResponse(string $messageId, Block $block): ?GetMessageResponse
    {
        try {
            return $this->client->getMessage($messageId, $block->getId());
        } catch (CommandException $ex) {
            $this->addExceptionToLog($ex, sprintf('get_message (%s)', $messageId), $block);
        }

        return null;
    }

    /**
     * @param Message $message
     * @param array $transactions
     */
    private function addTransactionsFromMessage(Message $message, array $transactions): void
    {
        /** @var ArrayableInterface $transaction */
        foreach ($transactions as $transaction) {
            $this->logger->info(sprintf("Processing TX %s", $transaction->toArray()['_id']));
            if ($transaction instanceof ConnectionTransaction) {
                $transaction->setTime($message->getTime());
            }
            $this->databaseMigration->addTransaction($transaction);
            ++$this->importerResult->transactions;
        }
    }

    private const RELEASE = [
        ['from' => '2021-05-01', 'to' => '2023-04-01', 'amount' => 2310500],
        ['from' => '2021-05-01', 'to' => '2023-04-01', 'amount' => 1155250],
        ['from' => '2021-05-01', 'to' => '2022-03-01', 'amount' => 1155250],
        ['from' => '2021-05-01', 'to' => '2023-04-01', 'amount' => 4621000],
        ['from' => '2021-05-01', 'to' => '2023-04-01', 'amount' => 2310500],
        ['from' => '2021-05-01', 'to' => '2022-03-01', 'amount' => 1155250],
        ['from' => '2021-12-01', 'to' => '2023-04-01', 'amount' => 3465750],
        ['from' => '2022-03-01', 'to' => '2023-04-01', 'amount' => 2310500],
        ['from' => '2022-03-01', 'to' => '2023-04-01', 'amount' => 3465750],
        ['from' => '2022-03-01', 'to' => '2023-04-01', 'amount' => 1155250],
    ];

    private static function getMonthDiff(DateTime $now, DateTime $base)
    {
        $diff = $base->diff($now);

        return ($diff->invert ? -1 : 1) * ($diff->format("%y") * 12 + $diff->format("%m") * 1 + 1);
    }

    private function updateInfo(GetBlockResponse $blockResponse): void
    {
        $info = new Info($this->genesisTime, $this->blockLength);

        $info->setLastBlockId($blockResponse->getBlock()->getId());
        $info->setTotalSupply($this->totalSupply / 10 ** $this->amountPrecision);

        $supply = 0;
        /** @var Node $node */
        foreach ($blockResponse->getBlock()->getNodes() as $node) {
            if ($node->isSpecial()) {
                continue;
            }
            $supply += $node->getBalance();
        }

        $circulatingSupply = $supply;
        foreach ($this->nonCirculatingAccounts as $address) {
            /** @var Account $account */
            $account = $this->client->getAccount($address)->getAccount();
            if ($account !== null) {
                $circulatingSupply -= $account->getBalance();
            }
        }

        foreach (self::RELEASE as $schedule) {
            $from = new DateTime($schedule['from'], new \DateTimeZone('UTC'));
            $to = new DateTime($schedule['to'], new \DateTimeZone('UTC'));
            $now = new DateTime('now', new \DateTimeZone('UTC'));
            $y = self::getMonthDiff($to, $from);
            $x = min($y, max(0, self::getMonthDiff($now, $from)));
            $progress = $x / $y;
            $circulatingSupply -= (1 - $progress) * $schedule['amount'] * (10 ** $this->amountPrecision);
        }

        $info->setCirculatingSupply($circulatingSupply / 10 ** $this->amountPrecision);
        $info->setUnpaidDividend(($this->totalSupply - $supply) / 10 ** $this->amountPrecision);

        $this->databaseMigration->addOrUpdateInfo($info);
    }

    private function addExceptionToLog(Throwable $exception, string $message, ?Block $block = null): void
    {
        $context = [
            'error_code' => $exception->getCode(),
            'error_message' => CommandError::getMessageByCode($exception->getCode()),
        ];

        if ($block) {
            $context['block'] = $block->getId();
        }

        $pattern = '[ADS Synchronization] %s - %s';

        $this->logger->error(sprintf($pattern, $message, $exception->getMessage()), $context);
    }


    private function getCurrentBlockId(): string
    {
        $now = time();
        return strtoupper(dechex($now - $now % $this->blockLength));
    }

    private function isDivBlock(string $blockId): bool
    {
        return 0 === hexdec($blockId) % (self::DIV_BLOCKS * $this->blockLength);
    }

    public function getResult(): ImporterResult
    {
        return $this->importerResult;
    }
}

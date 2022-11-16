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

declare(strict_types=1);

namespace Adshares\AdsOperator\AdsImporter\Database;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\AdsOperator\Document\Info;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Snapshot;
use Adshares\AdsOperator\Document\SnapshotAccount;
use Adshares\AdsOperator\Document\SnapshotNode;
use Doctrine\MongoDB\Cursor;

/**
 * DatabaseMigrationInterface should be implemented by every database engine.
 *
 * @package Adshares\AdsOperator\AdsImporter\Database
 */
interface DatabaseMigrationInterface
{
    /**
     * @param Info $info
     */
    public function addOrUpdateInfo(Info $info): void;

    /**
     * @param Message $message
     */
    public function addMessage(Message $message): void;

    /**
     * @param Block $block
     * @param int $blockLength
     */
    public function addBlock(Block $block, int $blockLength): void;

    /**
     * @param ArrayableInterface $transaction
     */
    public function addTransaction(ArrayableInterface $transaction): void;

    /**
     * @param Node $node
     */
    public function addOrUpdateNode(Node $node): void;

    /**
     * @param string $nodeId
     * @return null|string
     */
    public function getNodeVersion(string $nodeId): ?string;

    /**
     * @param string $nodeId
     * @return int
     */
    public function getNodeTransactionCount(string $nodeId): int;

    public function getNodes(): array;

    /**
     * @param Account $account
     * @param Node $node
     */
    public function addOrUpdateAccount(Account $account, Node $node): void;

    public function getAccounts(): array;

    /**
     * @param string $accountId
     * @return int
     */
    public function getAccountTransactionCount(string $accountId): int;

    /**
     * @param string $accountId
     * @return Cursor
     */
    public function getAccountTransactions(string $accountId): Cursor;

    public function deleteAccountTransaction(string $id);

    public function getTransaction(string $txid);

    public function getLatestBlockId(): ?string;

    public function getAllAccounts(): Cursor;

    public function getBlock(string $blockId);

    public function getSnapshot(string $snapshotId);

    public function addOrUpdateSnapshot(Snapshot $snapshot): void;

    public function addOrUpdateSnapshotNode(SnapshotNode $node): void;

    public function addOrUpdateSnapshotAccount(SnapshotAccount $account): void;
}

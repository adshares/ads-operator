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

namespace Adshares\AdsOperator\AdsImporter\Database;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\AdsOperator\Document\Info;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Message;

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

    /**
     * @param Account $account
     * @param Node $node
     */
    public function addOrUpdateAccount(Account $account, Node $node): void;

    /**
     * @param string $accountId
     * @return int
     */
    public function getAccountTransactionCount(string $accountId): int;

    /**
     * @return int|null
     */
    public function getNewestBlockTime():? int;
}

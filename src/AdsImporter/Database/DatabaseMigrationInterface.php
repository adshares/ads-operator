<?php


namespace Adshares\AdsOperator\AdsImporter\Database;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Message;

interface DatabaseMigrationInterface
{
    /**
     * @param Message $message
     */
    public function addMessage(Message $message): void;

    /**
     * @param Block $block
     */
    public function addBlock(Block $block): void;

    /**
     * @param ArrayableInterface $transaction
     */
    public function addTransaction(ArrayableInterface $transaction): void;

    /**
     * @param Node $node
     */
    public function addOrUpdateNode(Node $node): void;

    /**
     * @param Account $account
     * @param Node $node
     */
    public function addOrUpdateAccount(Account $account, Node $node): void;

    /**
     * @return int|null
     */
    public function getNewestBlockTime():? int;
}

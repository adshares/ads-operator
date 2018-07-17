<?php


namespace Adshares\AdsOperator\AdsImporter\Database;

use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Package;
use Adshares\AdsOperator\Document\Transaction;

interface DatabaseMigrationInterface
{
    /**
     * @param Package $package
     * @param Block $block
     */
    public function addPackage(Package $package, Block $block): void;

    /**
     * @param Block $block
     */
    public function addBlock(Block $block): void;

    /**
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction): void;

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

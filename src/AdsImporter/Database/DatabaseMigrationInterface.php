<?php


namespace Adshares\AdsManager\AdsImporter\Database;

use Adshares\Ads\Entity\Account;
use Adshares\Ads\Entity\Node;
use Adshares\AdsManager\Document\Block;
use Adshares\AdsManager\Document\Package;
use Adshares\AdsManager\Document\Transaction;

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

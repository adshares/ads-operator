<?php


namespace Adshares\AdsManager\Service;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CommandError;
use Adshares\Ads\Entity\Account;
use Adshares\Ads\Entity\Node;
use Adshares\Ads\Response\GetAccountsResponse;
use Adshares\Ads\Response\GetBlockResponse;
use Adshares\AdsManager\Document\Block;
use Adshares\AdsManager\Document\Package;
use Adshares\Ads\Exception\CommandException;
use Adshares\Ads\Response\GetPackageListResponse;
use Adshares\AdsManager\BlockExplorer\Database\DatabaseMigrationInterface;
use Adshares\AdsManager\Document\Transaction;
use Adshares\AdsManager\Helper\NumericalTransformation;
use Psr\Log\LoggerInterface;

class SynchronizeADSData
{
    /**
     * @var AdsClient
     */
    private $client;

    /**
     * @var DatabaseMigrationInterface
     */
    private $databaseMigration;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $genesisTime;

    /**
     * @var int
     */
    private $blockSeqTime = 32;

    /**
     * SynchronizeADSData constructor.
     * @param AdsClient $client
     * @param DatabaseMigrationInterface $databaseMigration
     * @param LoggerInterface $logger
     * @param int $genesisTime
     * @param int $blockSeqTime
     */
    public function __construct(
        AdsClient $client,
        DatabaseMigrationInterface $databaseMigration,
        LoggerInterface $logger,
        int $genesisTime,
        int $blockSeqTime
    ) {
        $this->client = $client;
        $this->databaseMigration = $databaseMigration;
        $this->logger = $logger;
        $this->genesisTime = $genesisTime;
        $this->blockSeqTime = $blockSeqTime;
    }

    public function sync(): void
    {
        $getMeResponse = $this->client->getMe();
        $startTime = $this->getStartTime();
        $endTime = (int)$getMeResponse->getPreviousBlockTime()->format('U');

        $blockId = NumericalTransformation::decToHex($startTime);
        $transactions = 0;

        // update nodes
        $this->updateNodes();

        do {
            try {
                $blockResponse = $this->client->getBlock($blockId);
                $block = $blockResponse->getBlock();

                if ($block instanceof Block) {
                    $blockTransactions = $this->addPackagesFromBlock($block);

                    $block->setTransactionCount($blockTransactions);
                    $this->databaseMigration->addBlock($block);

                    $transactions += $blockTransactions;
                }
            } catch (CommandException $ex) {
                if ($ex->getCode() !== CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                    $this->addExceptionToLog($ex, 'get_block', ['block' => $blockId]);
                }
            }

            $startTime += $this->blockSeqTime;
            $blockId = NumericalTransformation::decToHex($startTime);
        } while ($startTime <= $endTime);
    }

    private function getStartTime(): int
    {
        $from = $this->databaseMigration->getNewestBlockTime();

        if ($from) {
            return $from + $this->blockSeqTime;
        }

        return $this->genesisTime;
    }

    private function updateNodes(): void
    {
        $blockResponse = $this->client->getBlock();

        if ($blockResponse instanceof GetBlockResponse) {
            $nodes = $blockResponse->getBlock()->getNodes();

            /** @var Node $node */
            foreach ($nodes as $node) {
                if ($node->getId() === '0000') { // special node
                    continue;
                }

                $accountResponse = $this->client->getAccounts($node->getId());

                if ($accountResponse instanceof GetAccountsResponse) {
                    $accounts = $accountResponse->getAccounts();

                    /** @var Account $account */
                    foreach ($accounts as $account) {
                        $this->databaseMigration->addOrUpdateAccount($account, $node);
                    }
                }

                $this->databaseMigration->addOrUpdateNode($node);
            }
        }
    }

    private function addPackagesFromBlock(Block $block)
    {
        $blockTransactionsCount = 0;

        try {
            $packagesResponse = $this->client->getPackageList($block->getId());

            if ($packagesResponse instanceof GetPackageListResponse) {
                $packages = $packagesResponse->getPackages();


                /** @var Package $package */
                foreach ($packages as $package) {
                    $package->generateId();

                    $transactionsCount = $this->addTransactionsFromPackage($package, $block);
                    $package->setTransactionCount($transactionsCount);
                    $this->databaseMigration->addPackage($package, $block);

                    $blockTransactionsCount += $transactionsCount;
                }
            }
        } catch (CommandException $ex) {
            $this->addExceptionToLog($ex, 'get_package_list', ['block' => $block->getId()]);
        }

        return $blockTransactionsCount;
    }

    private function addTransactionsFromPackage(Package $package, Block $block): int
    {
        $transactionsCount = 0;
        try {
            $packageResponse = $this->client->getPackage(
                $package->getNode(),
                $package->getNodeMsid(),
                $block->getId()
            );

            $transactions = $packageResponse->getTransactions();

            if (count($transactions) > 0) {
                /** @var Transaction $transaction */
                foreach ($transactions as &$transaction) {
                    $transaction->setBlockId($block->getId());
                    $transaction->setPackageId($package->getId());

                    $this->databaseMigration->addTransaction($transaction);
                    ++$transactionsCount;
                }
            }
        } catch (CommandException $ex) {
            $this->addExceptionToLog($ex, 'get_package', [
                'block' => $block->getId(),
                'package' => $package->getId(),
                'node' => $package->getNode(),
                'node_msid' => $package->getNodeMsid()
            ]);
        }

        return $transactionsCount;
    }

    /**
     * @param CommandException $exception
     * @param string $type
     * @param array $context
     */
    private function addExceptionToLog(CommandException $exception, string $type, array $context): void
    {
        $message = '[ADS Synchronization] %s Failed: %s';
        $context = array_merge($context, [
            'error_code' => $exception->getCode(),
            'error_message' => CommandError::getMessageByCode($exception->getCode()),
        ]);

        $this->logger->error(sprintf($message, $type, $exception->getMessage()), $context);
    }
}

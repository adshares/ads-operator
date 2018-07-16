<?php


namespace Adshares\AdsManager\AdsImporter;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CommandError;
use Adshares\AdsManager\AdsImporter\Exception\AdsClientException;
use Adshares\AdsManager\Document\Block;
use Adshares\AdsManager\Document\Package;
use Adshares\AdsManager\Document\Node;
use Adshares\AdsManager\Document\Account;
use Adshares\Ads\Exception\CommandException;
use Adshares\AdsManager\AdsImporter\Database\DatabaseMigrationInterface;
use Adshares\AdsManager\Document\Transaction;
use Adshares\AdsManager\Helper\NumericalTransformation;
use Psr\Log\LoggerInterface;

class Importer
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
     * @var ImporterResult
     */
    private $importerResult;

    /**
     * Importer constructor.
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
        $this->importerResult = new ImporterResult();
    }

    public function import(): ImporterResult
    {
        $getMeResponse = $this->client->getMe();
        $startTime = $this->getStartTime();
        $endTime = (int)$getMeResponse->getPreviousBlockTime()->format('U');

        $blockId = NumericalTransformation::decToHex($startTime);

        // update nodes
        $this->updateNodes();

        do {
            try {
                $blockResponse = $this->client->getBlock($blockId);
                /** @var Block $block */
                $block = $blockResponse->getBlock();

                $blockTransactions = $this->addPackagesFromBlock($block);

                $block->setTransactionCount($blockTransactions);
                $this->databaseMigration->addBlock($block);
                ++$this->importerResult->blocks;
            } catch (CommandException $ex) {
                if ($ex->getCode() !== CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                    $this->addExceptionToLog($ex, 'get_block', ['block' => $blockId]);
                }
            }


            $startTime += $this->blockSeqTime;
            $blockId = NumericalTransformation::decToHex($startTime);
        } while ($startTime <= $endTime);

        return $this->importerResult;
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
        try {
            $blockResponse = $this->client->getBlock();
        } catch (CommandException $ex) {
            throw new AdsClientException('Cannot proceed importing data');
        }

        $nodes = $blockResponse->getBlock()->getNodes();

        /** @var Node $node */
        foreach ($nodes as $node) {
            if ($node->getId() === '0000') { // special node
                continue;
            }

            $this->updateAccounts($node);

            $this->databaseMigration->addOrUpdateNode($node);
            ++$this->importerResult->nodes;
        }
    }

    /**
     * @param Node $node
     *
     */
    private function updateAccounts(Node $node): void
    {
        $accountResponse = $this->client->getAccounts((int)$node->getId());
        $accounts = $accountResponse->getAccounts();

        /** @var Account $account */
        foreach ($accounts as $account) {
            $this->databaseMigration->addOrUpdateAccount($account, $node);
            ++$this->importerResult->accounts;
        }
    }

    private function addPackagesFromBlock(Block $block): int
    {
        $blockTransactionsCount = 0;

        try {
            $packagesResponse = $this->client->getPackageList($block->getId());
            $packages = $packagesResponse->getPackages();

            /** @var Package $package */
            foreach ($packages as $package) {
                $package->generateId();

                $transactionsCount = $this->addTransactionsFromPackage($package, $block);

                $package->setTransactionCount($transactionsCount);
                $this->databaseMigration->addPackage($package, $block);

                ++$this->importerResult->packages;
                $blockTransactionsCount += $transactionsCount;
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
                    ++$this->importerResult->transactions;
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

    public function getResult(): ImporterResult
    {
        return $this->importerResult;
    }
}

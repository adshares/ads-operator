<?php


namespace Adshares\AdsOperator\AdsImporter;

use Adshares\Ads\AdsClient;
use Adshares\Ads\Driver\CommandError;
use Adshares\Ads\Entity\Transaction\AbstractTransaction;
use Adshares\Ads\Response\GetMessageResponse;
use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\Ads\Exception\CommandException;
use Adshares\AdsOperator\AdsImporter\Database\DatabaseMigrationInterface;
use Adshares\AdsOperator\Helper\NumericalTransformation;
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

        $this->updateNodes();

        do {
            try {
                $blockResponse = $this->client->getBlock($blockId);
                /** @var Block $block */
                $block = $blockResponse->getBlock();

                if ($block->getMessageCount() > 0) {
                    $blockTransactions = $this->addMessagesFromBlock($block);

                    $block->setTransactionCount($blockTransactions);
                    $this->databaseMigration->addBlock($block);
                    ++$this->importerResult->blocks;
                }
            } catch (CommandException $ex) {
                if ($ex->getCode() !== CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                    $this->addExceptionToLog($ex, sprintf('get_block (%s)', $blockId));
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
            if ($ex->getCode() !== CommandError::GET_BLOCK_INFO_UNAVAILABLE) {
                throw new AdsClientException('Cannot proceed importing data: '.$ex->getMessage());
            }

            return;
        }

        $nodes = $blockResponse->getBlock()->getNodes();

        /** @var Node $node */
        foreach ($nodes as $node) {
            if ($node->isSpecial()) {
                continue;
            }

            $this->updateAccounts($node);

            $this->databaseMigration->addOrUpdateNode($node);
            ++$this->importerResult->nodes;
        }
    }

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

    private function addMessagesFromBlock(Block $block): int
    {
        $blockTransactionsCount = 0;

        try {
            $messageIdResponse = $this->client->getMessageIds($block->getId());
            $messageIds = $messageIdResponse->getMessageIds();

            foreach ($messageIds as $messageId) {
                $messageResponse = $this->getMessageResponse($messageId, $block);
                /** @var Message $message */
                $message = $messageResponse->getMessage();
                $transactions = $messageResponse->getTransactions();
                $transactionsCount = count($transactions);

                $message->setTransactionCount($transactionsCount);
                $this->databaseMigration->addMessage($message);
                ++$this->importerResult->messages;

                if ($transactions) {
                    $this->addTransactionsFromMessage($transactions);
                    $blockTransactionsCount += $transactionsCount;
                }
            }
        } catch (CommandException $ex) {
            $this->addExceptionToLog($ex, 'get_message_ids', $block);
        }

        return $blockTransactionsCount;
    }

    private function getMessageResponse(string $messageId, Block $block): GetMessageResponse
    {
        try {
            return $this->client->getMessage($messageId, $block->getId());
        } catch (CommandException $ex) {
            $this->addExceptionToLog($ex, sprintf('get_message (%s)', $messageId), $block);
        }
    }

    private function addTransactionsFromMessage(array $transactions): void
    {
        /** @var AbstractTransaction $transaction */
        foreach ($transactions as $transaction) {
            $this->databaseMigration->addTransaction($transaction);

            ++$this->importerResult->transactions;
        }
    }

    private function addExceptionToLog(CommandException $exception, string $message, ?Block $block = null): void
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

    public function getResult(): ImporterResult
    {
        return $this->importerResult;
    }
}

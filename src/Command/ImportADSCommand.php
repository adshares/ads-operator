<?php


namespace Adshares\AdsOperator\Command;

use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\AdsImporter\Importer;
use Adshares\AdsOperator\AdsImporter\ImporterResult;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportADSCommand extends ContainerAwareCommand
{
    private $adsImporter;

    public function __construct(Importer $adsImporter)
    {
        $this->adsImporter = $adsImporter;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ads:import')
            ->setDescription('Importing data from ADS client');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            /** @var ImporterResult $result */
            $result = $this->adsImporter->import();
        } catch (AdsClientException $ex) {
            $output->writeln('Import cannot be proceed');
            return;
        }

        $output->writeln(sprintf(
            'Imported %s blocks, %s messages, %s transactions, %s nodes, %s accounts',
            $result->blocks,
            $result->messages,
            $result->transactions,
            $result->nodes,
            $result->accounts
        ));
    }
}

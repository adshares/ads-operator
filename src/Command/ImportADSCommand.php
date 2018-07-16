<?php


namespace Adshares\AdsManager\Command;

use Adshares\AdsManager\AdsImporter\Exception\AdsClientException;
use Adshares\AdsManager\AdsImporter\Importer;
use Adshares\AdsManager\AdsImporter\ImporterResult;
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
            sleep(3); // retry for new block
            $result = $this->adsImporter->import();
        }

        $output->writeln(sprintf(
            'Imported %s blocks, %s packages, %s transactions, %s nodes, %s accounts',
            $result->blocks,
            $result->packages,
            $result->transactions,
            $result->nodes,
            $result->accounts
        ));
    }
}

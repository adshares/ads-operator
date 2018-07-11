<?php


namespace Adshares\AdsManager\Command;

use Adshares\AdsManager\Service\SynchronizeADSData;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizationADSCommand extends ContainerAwareCommand
{
    private $synchronizationService;

    public function __construct(SynchronizeADSData $synchronizationService)
    {
        $this->synchronizationService = $synchronizationService;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ads:synchronize')
            ->setDescription('Synchronize ADS data');
    }
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->synchronizationService->sync();

        $output->writeln('[ADS] Synchronization successfully completed');
    }
}

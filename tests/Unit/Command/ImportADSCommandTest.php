<?php


namespace Adshares\AdsOperator\Tests\Unit\Command;

use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\AdsImporter\Importer;
use Adshares\AdsOperator\AdsImporter\ImporterResult;
use Adshares\AdsOperator\Command\ImportADSCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class ImportADSCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::bootKernel();
        $application = new Application($kernel->getName());

        $result = new ImporterResult();
        $result->blocks = 10;
        $result->messages = 20;
        $result->transactions = 111;
        $result->nodes = 3;
        $result->accounts = 10;

        $importer = $this->createMock(Importer::class);
        $importer
            ->method('import')
            ->willReturn($result);

        $application->add(new ImportADSCommand($importer));

        $command = $application->find('ads:import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        $output = $commandTester->getDisplay();

        $this->assertContains('10 blocks', $output);
        $this->assertContains('20 packages', $output);
        $this->assertContains('111 transactions', $output);
        $this->assertContains('3 nodes', $output);
        $this->assertContains('10 accounts', $output);
    }

    public function testSecondExecutionWhenImporterThrowsException()
    {
        $kernel = static::bootKernel();
        $application = new Application($kernel->getName());

        $importer = $this->createMock(Importer::class);
        $importer
            ->method('import')
            ->will($this->throwException(new AdsClientException('')));

        $application->add(new ImportADSCommand($importer));

        $command = $application->find('ads:import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        $output = $commandTester->getDisplay();

        $this->assertContains('Import cannot be proceed', $output);
    }
}

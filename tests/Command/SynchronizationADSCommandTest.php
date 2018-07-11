<?php


namespace App\Tests\Command;

use App\Command\SynchronizationADSCommand;
use App\Service\SynchronizeADSData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class SynchronizationADSCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::bootKernel();
        $application = new Application($kernel->getName());

        $application->add(new SynchronizationADSCommand($this->createMock(SynchronizeADSData::class)));

        $command = $application->find('app:synchronize-ads');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('successfully completed', $output);
    }
}
<?php


namespace Adshares\AdsManager\Tests\Unit\Command;

use Adshares\AdsManager\Command\SynchronizationADSCommand;
use Adshares\AdsManager\Service\SynchronizeADSData;
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

        $command = $application->find('ads:synchronize');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('successfully completed', $output);
    }
}
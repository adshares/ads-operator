<?php

/**
 * Copyright (c) 2018-2022 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types=1);

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

        $this->assertStringContainsString('10 blocks', $output);
        $this->assertStringContainsString('20 messages', $output);
        $this->assertStringContainsString('111 transactions', $output);
        $this->assertStringContainsString('3 nodes', $output);
        $this->assertStringContainsString('10 accounts', $output);
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

        $this->assertStringContainsString('The command is already running in another', $output);
    }
}

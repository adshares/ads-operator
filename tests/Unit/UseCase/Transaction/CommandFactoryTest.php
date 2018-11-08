<?php
/**
 * Copyright (C) 2018 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator.  If not, see <https://www.gnu.org/licenses/>
 */

namespace Adshares\AdsOperator\Tests\Unit\UseCase\Transaction;

use Adshares\Ads\Command\ChangeAccountKeyCommand;
use Adshares\AdsOperator\UseCase\Exception\UnsupportedTransactionException;
use Adshares\AdsOperator\UseCase\Transaction\CommandFactory;
use PHPUnit\Framework\TestCase;

class CommandFactoryTest extends TestCase
{
    public function testCreateCommandWhenTypeIsUnsupported()
    {
        $this->expectException(UnsupportedTransactionException::class);
        $type = 'unsupportedType';

        CommandFactory::create($type, []);
    }

    public function testCreateWhenChangeAccountKeyType()
    {
        $type = 'changeAccountKey';
        $this->assertInstanceOf(ChangeAccountKeyCommand::class, CommandFactory::create($type, []));
    }
}

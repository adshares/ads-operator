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

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testMessage(): void
    {
        $transactionCount = 10;
        $message = Message::create($transactionCount);

        $this->assertEquals($transactionCount, $message->getTransactionCount());
    }

    public function testValidation()
    {
        $ids = [
            "1234:12341234" => true,
            "1234:123412345" => false,
            "1234-12341234" => false,
            "1234-1234-1234" => false,
            "1234:1234" => false,
            "1234" => false,
        ];

        foreach ($ids as $id => $expected) {
            $this->assertEquals($expected, Message::validateId((string)$id));
        }
    }
}

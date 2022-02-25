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

use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Node;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    public function testBlock(): void
    {
        $id = '00001';
        $nodes = [
            Node::create('1'),
            Node::create('2'),
            Node::create('3'),
            Node::create('4'),
        ];
        $messageCount = 5;

        $block = Block::create($id, $nodes, $messageCount);

        $this->assertEquals($id, $block->getId());
        $this->assertEquals($nodes, $block->getNodes());
        $this->assertEquals($messageCount, $block->getMessageCount());

        $block->setTransactionCount(100);
        $this->assertEquals(100, $block->getTransactionCount());
    }

    public function testValidation()
    {
        $ids = [
            "12341234" => true,
            "123AF234" => true,
            "F2341230" => true,
            "F23412302" => false,
            "1234-1234-1234" => false,
            "1234:1234" => false,
            "1234" => false,
        ];

        foreach ($ids as $id => $expected) {
            $this->assertEquals($expected, Block::validateId((string)$id));
        }
    }
}

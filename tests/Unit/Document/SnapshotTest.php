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

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Snapshot;
use Adshares\AdsOperator\Document\Node;
use PHPUnit\Framework\TestCase;

final class SnapshotTest extends TestCase
{
//    public function testBlock(): void
//    {
//        $id = '00001';
//        $nodes = [
//            Node::create('1'),
//            Node::create('2'),
//            Node::create('3'),
//            Node::create('4'),
//        ];
//        $messageCount = 5;
//
//        $snapshot = Snapshot::create($id, $nodes, $messageCount);
//
//        $this->assertEquals($id, $snapshot->getId());
//        $this->assertEquals($nodes, $snapshot->getNodes());
//        $this->assertEquals($messageCount, $snapshot->getMessageCount());
//
//        $snapshot->setTransactionCount(100);
//        $this->assertEquals(100, $snapshot->getTransactionCount());
//    }

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
            $this->assertEquals($expected, Snapshot::validateId($id));
        }
    }
}

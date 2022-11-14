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

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Snapshot;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class SnapshotTest extends TestCase
{
    public function testSnapshot(): void
    {
        $id = '00001';
        $time = new DateTimeImmutable('-5 days');

        $snapshot = Snapshot::create($id, $time);

        $this->assertEquals($id, $snapshot->getId());
        $this->assertEquals($time, $snapshot->getTime());
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
            $this->assertEquals($expected, Snapshot::validateId($id));
        }
    }
}

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

namespace Adshares\AdsOperator\Document;

use DateTimeImmutable;
use DateTimeInterface;

class Snapshot
{
    protected string $id;

    protected DateTimeInterface $time;

    public function getId(): string
    {
        return $this->id;
    }

    public function getTime(): DateTimeInterface
    {
        return $this->time;
    }

    public static function validateId(string $id): bool
    {
        return (bool)preg_match('/^[0-9A-F]{8}$/', $id);
    }

    public static function create(?string $id = null): self
    {
        $x = new self();
        if (null !== $id) {
            $x->id = $id;
            $x->time = new DateTimeImmutable('@' . hexdec($id));
        }
        return $x;
    }
}

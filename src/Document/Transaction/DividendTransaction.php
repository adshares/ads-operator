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

declare(strict_types=1);

namespace Adshares\AdsOperator\Document\Transaction;

use Adshares\AdsOperator\Document\ArrayableInterface;
use Adshares\Ads\Entity\Transaction\EmptyTransaction as BaseEmptyTransaction;

/**
 * Class EmptyTransaction
 * @package Adshares\AdsOperator\Document\Transaction
 */
class DividendTransaction extends BaseEmptyTransaction implements ArrayableInterface
{
    /**
     * @var string
     */
    protected $targetAddress;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var \DateTime
     */
    protected $time;

    /**
     * @return null|string
     */
    public function getTargetAddress(): ?string
    {
        return $this->targetAddress;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    public function fillWithRawData(array $data): void
    {
        parent::fillWithRawData($data);

        if (!$this->time) {
            $this->time = new \DateTime('@' . hexdec($this->blockId));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            '_id' => $this->id,
            'type' => $this->type,
            'blockId' => $this->blockId,
            'time' => $this->time,
            'targetAddress' => $this->targetAddress,
            'amount' => $this->amount,
        ];
    }
}

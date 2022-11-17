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

use DateTimeInterface;

/**
 * Class Account
 * @package Adshares\AdsOperator\Document
 */
class Account extends \Adshares\Ads\Entity\Account
{
    protected string $id;
    protected string $nodeId;
    protected int $messageCount;
    protected int $transactionCount;
    protected ?string $label = null;
    protected ?string $icon = null;

    /**
     * Account constructor.
     */
    public static function create(?string $address = null): self
    {
        $x = new self();
        if (null !== $address) {
            $x->id = $address;
            $x->address = $address;
        }
        return $x;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMessageCount(): int
    {
        return $this->messageCount;
    }

    public function setBalance(int $balance)
    {
        $this->balance = $balance;
    }

    public function setRemoteChange(DateTimeInterface $value)
    {
        $this->remoteChange = $value;
    }

    public function setTransactionCount(int $transactionCount)
    {
        $this->transactionCount = $transactionCount;
    }

    public function getTransactionCount(): int
    {
        return $this->transactionCount;
    }

    public function fillWithRawData(array $data): void
    {
        parent::fillWithRawData($data);
        $this->messageCount = $this->msid;
    }

    public static function validateId(string $id): bool
    {
        return (bool) preg_match('/^[0-9A-F]{4}-[0-9A-F]{8}-[0-9A-F]{4}$/', $id);
    }
}

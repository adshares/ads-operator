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

namespace Adshares\AdsOperator\Document;

/**
 * Class Account
 * @package Adshares\AdsOperator\Document
 */
class Account extends \Adshares\Ads\Entity\Account
{
    protected $id;

    protected $nodeId;

    /**
     * @var int
     */
    protected $messageCount;

    /**
     * @var int
     */
    protected $transactionCount;

    /**
     * Account constructor.
     * @param string|null $address
     */
    public static function create(string $address = null): self
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

    /**
     * @return int
     */
    public function getMessageCount(): int
    {
        return $this->messageCount;
    }

    /**
     * @param int $transactionCount
     */
    public function setTransactionCount(int $transactionCount)
    {
        $this->transactionCount = $transactionCount;
    }

    /**
     * @return int
     */
    public function getTransactionCount(): int
    {
        return $this->transactionCount;
    }

    /**
     * @param array $data
     */
    public function fillWithRawData(array $data): void
    {
        parent::fillWithRawData($data);
        $this->messageCount = $this->msid;
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function validateId(string $id): bool
    {
        return (bool) preg_match('/^[0-9A-F]{4}-[0-9A-F]{8}-[0-9A-F]{4}$/', $id);
    }
}

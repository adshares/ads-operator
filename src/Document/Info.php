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
 * Class Info
 * @package Adshares\AdsOperator\Document
 */
class Info
{
    /**
     * @var int
     */
    protected $genesisTime;

    /**
     * @var int
     */
    protected $blockLength;

    /**
     * @var string
     */
    protected $lastBlockId;

    /**
     * @var float
     */
    protected $totalSupply;

    /**
     * @var float
     */
    protected $circulatingSupply;

    /**
     * @var float
     */
    protected $unpaidDividend;

    /**
     * Info constructor.
     * @param int|null $genesisTime
     * @param int|null $blockLength
     */
    public function __construct(int $genesisTime = null, int $blockLength = null)
    {
        if (null !== $genesisTime) {
            $this->genesisTime = $genesisTime;
        }
        if (null !== $blockLength) {
            $this->blockLength = $blockLength;
        }
    }

    /**
     * @return int
     */
    public function getGenesisTime(): int
    {
        return $this->genesisTime;
    }

    /**
     * @param int $genesisTime
     */
    public function setGenesisTime(int $genesisTime): void
    {
        $this->genesisTime = $genesisTime;
    }

    /**
     * @return int
     */
    public function getBlockLength(): int
    {
        return $this->blockLength;
    }

    /**
     * @param int $blockLength
     */
    public function setBlockLength(int $blockLength): void
    {
        $this->blockLength = $blockLength;
    }

    /**
     * @return string
     */
    public function getLastBlockId(): string
    {
        return $this->lastBlockId;
    }

    /**
     * @param string $lastBlockId
     */
    public function setLastBlockId(string $lastBlockId): void
    {
        $this->lastBlockId = $lastBlockId;
    }

    /**
     * @return float
     */
    public function getTotalSupply(): float
    {
        return $this->totalSupply;
    }

    /**
     * @param float $totalSupply
     */
    public function setTotalSupply(float $totalSupply): void
    {
        $this->totalSupply = $totalSupply;
    }

    /**
     * @return float
     */
    public function getCirculatingSupply(): float
    {
        return $this->circulatingSupply;
    }

    /**
     * @param float $circulatingSupply
     */
    public function setCirculatingSupply(float $circulatingSupply): void
    {
        $this->circulatingSupply = $circulatingSupply;
    }

    /**
     * @return float
     */
    public function getUnpaidDividend(): float
    {
        return $this->unpaidDividend;
    }

    /**
     * @param float $unpaidDividend
     */
    public function setUnpaidDividend(float $unpaidDividend): void
    {
        $this->unpaidDividend = $unpaidDividend;
    }
}

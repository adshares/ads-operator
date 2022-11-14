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

namespace Adshares\AdsOperator\Document;

use DateTime;

class ExchangeRateHistory
{
    /** @var string  */
    private $id;
    /** @var DateTime */
    private $date;
    /** @var float */
    private $rate;
    /** @var string */
    private $currency;
    /** @var string */
    private $provider;

    public function __construct(DateTime $date, float $rate, string $provider, string $currency = 'USD')
    {
        $this->date = $date;
        $this->rate = $rate;
        $this->currency = $currency;
        $this->provider = $provider;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}

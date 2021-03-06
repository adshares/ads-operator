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

namespace Adshares\AdsOperator\Repository\Doctrine;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Adshares\AdsOperator\Repository\InfoRepositoryInterface;
use Adshares\AdsOperator\Document\Info;

/**
 * Class InfoRepository
 * @package Adshares\AdsOperator\Repository\Doctrine
 */
class InfoRepository extends DocumentRepository implements InfoRepositoryInterface
{
    /**
     * @param int $genesisTime
     * @return Info|null
     */
    public function getInfo(int $genesisTime):? Info
    {
        /** @var Info $info */
        $info = $this->find($genesisTime);

        return $info;
    }
}

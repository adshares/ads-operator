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

use Adshares\AdsOperator\Document\LocalTransaction;
use Adshares\AdsOperator\Repository\LocalTransactionRepositoryInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;

class LocalTransactionRepository extends DocumentRepository implements LocalTransactionRepositoryInterface
{

    public function add(LocalTransaction $transaction): void
    {
        $this->getDocumentManager()->persist($transaction);
        $this->getDocumentManager()->flush();
    }
}

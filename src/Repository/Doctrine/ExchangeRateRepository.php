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


namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Document\ExchangeRate;
use Adshares\AdsOperator\Repository\Exception\ExchangeRateNotFoundException;
use Adshares\AdsOperator\Repository\ExchangeRateRepositoryInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Exception;
use MongoDuplicateKeyException;

class ExchangeRateRepository extends DocumentRepository implements ExchangeRateRepositoryInterface
{
    public function addExchangeRate(ExchangeRate $exchangeRate): void
    {
        try {
            $this->getDocumentManager()->persist($exchangeRate);
            $this->getDocumentManager()->flush();
        } catch (Exception $exception) {
            throw new ExchangeRateNotFoundException(sprintf(
                'Duplicate entry for date `%s` and currency `%s`',
                $exchangeRate->getDate()->format('Y-m-d H:i'),
                $exchangeRate->getCurrency()
            ));
        }
    }
}

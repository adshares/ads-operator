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

namespace Adshares\AdsOperator\UseCase\Transaction;

use Adshares\Ads\Command\AbstractTransactionCommand;
use Adshares\AdsOperator\UseCase\Exception\UnsupportedTransactionException;

class FactoryCommand
{
    /**
     * @param string $type
     * @param array $params
     * @throws UnsupportedTransactionException
     * @return AbstractTransactionCommand
     */
    public static function create(string $type, array $params): AbstractTransactionCommand
    {
        $supportedTypes = [
            UserChangeKey::USER_CHANGE_ACCOUNT_KEY,
        ];

        if (!in_array($type, $supportedTypes)) {
            throw new UnsupportedTransactionException(sprintf('Unsupported transaction type: %s', $type));
        }

        $class = "\Adshares\Ads\Command\\".ucfirst($type)."Command";

        if (!class_exists($class)) {
            throw new UnsupportedTransactionException(sprintf('Class %s does not exist.', $class));
        }

        if ($type === UserChangeKey::USER_CHANGE_ACCOUNT_KEY) {
            $publicKey = $params['publicKey'] ?? '';
            $signature = $params['signature'] ?? '';

            return new $class($publicKey, $signature);
        }
    }
}

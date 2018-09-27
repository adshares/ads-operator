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

use Adshares\Ads\AdsClient;
use Adshares\Ads\Command\ChangeAccountKeyCommand;
use Adshares\AdsOperator\Document\User;

class UserChangeKey
{
    const DRY_RUN = true;

    private $client;

    public function __construct(AdsClient $client)
    {
        $this->client = $client;
    }

    public function change(User $user, string $address, string $publicKey, string $signature)
    {
        // check if $address belongs to $user
        if (!$user->isMyAccount($address)) {

        }

        $getAccount = $this->client->getAccount($address);

        $command = new ChangeAccountKeyCommand($publicKey, $signature);
        $response = $this->client->changeAccountKey($command, self::DRY_RUN);

        return $getAccount;
    }
}

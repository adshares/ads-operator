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

namespace Adshares\AdsOperator\Tests\Unit\UseCase;

use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\Exception\UserNotFoundException;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\UseCase\ConfirmChangeUserEmail;
use Adshares\AdsOperator\UseCase\Exception\BadTokenValueException;
use Adshares\AdsOperator\UseCase\Exception\UserExistsException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ConfirmChangeUserEmailTest extends TestCase
{
    public function testConfirmWhenTokenIsWrong()
    {
        $this->expectException(BadTokenValueException::class);

        $id = uniqid();
        $user = new User('adshares@adshares.net', sha1('test'));
        $user->setId($id);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($user);

        $confirmChangeUserEmail = new ConfirmChangeUserEmail(
            $userRepository,
            $this->createMock(LoggerInterface::class)
        );

        $confirmChangeUserEmail->confirm($id, uniqid());
    }

    public function testConfirmWhenEmailExists()
    {
        $this->expectException(UserExistsException::class);

        $id = uniqid();
        $user = new User('adshares@adshares.net', sha1('test'));
        $user->setId($id);

        $user->changeEmail('new@adshares.net');

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($user);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $confirmChangeUserEmail = new ConfirmChangeUserEmail(
            $userRepository,
            $this->createMock(LoggerInterface::class)
        );

        $confirmChangeUserEmail->confirm($id, $user->getToken());
    }

    public function testConfirmWhenTokenIsValidAndEmailDoesNotExist()
    {
        $id = uniqid();
        $user = new User('adshares@adshares.net', sha1('test'));
        $user->setId($id);

        $user->changeEmail('new@adshares.net');

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($user);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->will($this->throwException(new UserNotFoundException()));

        $userRepository
            ->expects($this->once())
            ->method('save');

        $confirmChangeUserEmail = new ConfirmChangeUserEmail(
            $userRepository,
            $this->createMock(LoggerInterface::class)
        );

        $confirmChangeUserEmail->confirm($id, $user->getToken());
    }
}

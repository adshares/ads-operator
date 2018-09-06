<?php
/**
 * Copyright (C) 2018 Adshares sp. z. o.o.
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

namespace Adshares\AdsOperator\Tests\Unit\Auth;

use Adshares\AdsOperator\Auth\Exception\UserAlreadyExistsException;
use Adshares\AdsOperator\Auth\UserRegistration;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\Validator\DocumentValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegistrationTest extends TestCase
{
    public function testWhenUserExistsInTheDatabase()
    {
        $this->expectException(UserAlreadyExistsException::class);
        $user  = new User('user@adshares.net', sha1('password'));

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $userRegistration = new UserRegistration($userRepository, $this->getValidatorMock());
        $userRegistration->register($user);
    }

    public function testWhenUserDoesNotExistInTheDatabaseAndShouldBeStore()
    {
        $user  = new User('user@adshares.net', sha1('password'));

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $userRepository
            ->expects($this->once())
            ->method('signUp');

        $userRegistration = new UserRegistration($userRepository, $this->getValidatorMock());
        $userRegistration->register($user);
    }

    private function getValidatorMock()
    {
        return $this->createMock(DocumentValidator::class);
    }
}

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

namespace Adshares\AdsOperator\Tests\Unit\UseCase;

use Adshares\AdsOperator\Auth\PasswordCheckerInterface;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\UseCase\ChangeUserPassword;
use Adshares\AdsOperator\UseCase\Exception\BadPasswordException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangeUserPasswordTest extends TestCase
{
    public function testWhenOldPasswordIsMismatched()
    {
        $this->expectException(BadPasswordException::class);

        $user  = new User('user@adshares.net', sha1('password'));
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $passwordChecker = $this->createMock(PasswordCheckerInterface::class);
        $passwordChecker
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(false);
        $passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $changeUserPassword = new ChangeUserPassword($userRepository, $passwordChecker, $passwordEncoder);
        $changeUserPassword->change($user, 'oldpassword', 'newpassword');
    }

    public function testWhenOldPasswordIsCorrect()
    {
        $user  = new User('user@adshares.net', sha1('password'));

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('save');

        $passwordChecker = $this->createMock(PasswordCheckerInterface::class);
        $passwordChecker
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true);
        $passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $passwordEncoder
            ->expects($this->once())
            ->method('encodePassword')
            ->willReturn(sha1('password'));

        $changeUserPassword = new ChangeUserPassword($userRepository, $passwordChecker, $passwordEncoder);
        $changeUserPassword->change($user, 'oldPassword', 'newpassword');
    }
}

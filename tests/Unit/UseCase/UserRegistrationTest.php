<?php

/**
 * Copyright (c) 2018-2022 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types=1);

namespace Adshares\AdsOperator\Tests\Unit\UseCase;

use Adshares\AdsOperator\UseCase\UserRegistration;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\Validator\DocumentValidator;
use Adshares\AdsOperator\Validator\ValidatorException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistrationTest extends TestCase
{
    public function testWhenValidationErrorsOccurs()
    {
        $this->expectException(ValidatorException::class);
        $user  = new User('user@adshares.net', sha1('password'));

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $validator = $this->createMock(DocumentValidator::class);
        $validator
            ->method('validate')
            ->willReturn([[], []]);

        $userRegistration = new UserRegistration($userRepository, $validator, $this->getEncoderMock());
        $userRegistration->register($user);
    }

    public function testWhenValidationDoesNotContainErrors()
    {
        $user  = new User('user@adshares.net', 'password');

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('signUp');

        $userRepository
            ->expects($this->once())
            ->method('signUp');

        $userRegistration = new UserRegistration(
            $userRepository,
            $this->getValidatorMock(),
            $this->getEncoderMock($user->getPassword())
        );
        $userRegistration->register($user);
    }

    private function getValidatorMock()
    {
        return $this->createMock(DocumentValidator::class);
    }

    private function getEncoderMock(?string $password = null)
    {
        $mock = $this->createMock(UserPasswordEncoderInterface::class);

        if ($password) {
            $mock
                ->method('encodePassword')
                ->willReturn(sha1($password));
        }

        return $mock;
    }
}

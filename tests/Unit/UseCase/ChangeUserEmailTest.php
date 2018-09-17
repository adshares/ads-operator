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
use Adshares\AdsOperator\Document\Exception\InvalidEmailException;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Queue\Exception\QueueCannotAddMessage;
use Adshares\AdsOperator\Queue\QueueInterface;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\UseCase\ChangeUserEmail;
use Adshares\AdsOperator\UseCase\Exception\BadPasswordException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ChangeUserEmailTest extends TestCase
{
    public function testChangeWhenEmailIsInvalid()
    {
        $this->expectException(InvalidEmailException::class);

        $repository = $this->createMock(UserRepositoryInterface::class);
        $queue = $this->createMock(QueueInterface::class);
        $passwordChecker = $this->createMock(PasswordCheckerInterface::class);
        $passwordChecker
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true);

        $logger = new NullLogger();
        $password = sha1('test');
        $user = new User('user@example.pl', $password);

        $changeUserEmail = new ChangeUserEmail($repository, $queue, $passwordChecker, $logger);
        $changeUserEmail->change($user, 'test.o2.pl', $password);
    }

    public function testChangeWhenPasswordIsInvalid()
    {
        $this->expectException(BadPasswordException::class);

        $repository = $this->createMock(UserRepositoryInterface::class);
        $queue = $this->createMock(QueueInterface::class);
        $passwordChecker = $this->createMock(PasswordCheckerInterface::class);
        $passwordChecker
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(false);

        $logger = new NullLogger();
        $user = new User('user@example.pl', sha1('test'));

        $changeUserEmail = new ChangeUserEmail($repository, $queue, $passwordChecker, $logger);
        $changeUserEmail->change($user, 'test.o2.pl', sha1('test2'));
    }

    public function testChangeWhenEmailIsValidButQueueThrowsAnException()
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('save');

        $queue = $this->createMock(QueueInterface::class);
        $queue
            ->expects($this->once())
            ->method('publish')
            ->will($this->throwException(new QueueCannotAddMessage()));

        $passwordChecker = $this->createMock(PasswordCheckerInterface::class);
        $passwordChecker
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error');

        $password = sha1('test');
        $user = new User('user@example.pl', $password);

        $changeUserEmail = new ChangeUserEmail($repository, $queue, $passwordChecker, $logger);
        $changeUserEmail->change($user, 'test@o2.pl', $password);
    }

    public function testChangeEmailWhenEmailAndQueueAreValid()
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('save');

        $queue = $this->createMock(QueueInterface::class);
        $queue
            ->expects($this->once())
            ->method('publish');

        $passwordChecker = $this->createMock(PasswordCheckerInterface::class);
        $passwordChecker
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->never())
            ->method('error');

        $password = sha1('test');
        $user = new User('user@example.pl', $password);

        $changeUserEmail = new ChangeUserEmail($repository, $queue, $passwordChecker, $logger);
        $changeUserEmail->change($user, 'test@o2.pl', $password);
    }
}

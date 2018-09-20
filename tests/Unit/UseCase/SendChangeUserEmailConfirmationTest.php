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

use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Mailer\MailerInterface;
use Adshares\AdsOperator\Repository\Exception\UserNotFoundException;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\Template\TemplateInterface;
use Adshares\AdsOperator\UseCase\SendChangeUserEmailConfirmation;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SendChangeUserEmailConfirmationTest extends TestCase
{
    public function testSendWhenUserExistsAndNoErrors()
    {
        $user = new User('user@adshares.net', sha1('test'));
        $user->changeEmail('new@adshares.net');
        $body = 'email body';

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $template = $this->createMock(TemplateInterface::class);
        $template
            ->expects($this->once())
            ->method('render')
            ->willReturn($body);

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->once())
            ->method('send');

        $sendChangeUserEmailConfirmation = new SendChangeUserEmailConfirmation(
            $userRepository,
            $mailer,
            $template,
            'templates/change-user-email.twig',
            'adshares@adshares.net',
            'Confirmation email',
            'http://ads-operator.ads/confirm',
            $this->createMock(LoggerInterface::class)
        );

        $sendChangeUserEmailConfirmation->send('new@adshares.net');
    }

    public function testSendWhenUserDoesNotExists()
    {
        $user = new User('user@adshares.net', sha1('test'));
        $user->changeEmail('new@adshares.net');

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->will($this->throwException(new UserNotFoundException()));

        $template = $this->createMock(TemplateInterface::class);
        $template
            ->expects($this->never())
            ->method('render');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->never())
            ->method('send');

        $sendChangeUserEmailConfirmation = new SendChangeUserEmailConfirmation(
            $userRepository,
            $mailer,
            $template,
            'templates/change-user-email.twig',
            'adshares@adshares.net',
            'Confirmation email',
            'http://ads-operator.ads/confirm',
            $this->createMock(LoggerInterface::class)
        );

        $sendChangeUserEmailConfirmation->send('new@adshares.net');
    }
}

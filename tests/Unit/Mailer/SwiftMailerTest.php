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

namespace Adshares\AdsOperator\Tests\Unit\Mailer;

use Adshares\AdsOperator\Mailer\Exception\CannotSendEmailException;
use Adshares\AdsOperator\Mailer\SwiftMailer;
use PHPUnit\Framework\TestCase;

class SwiftMailerTest extends TestCase
{
    public function testSendEmailWhenExceptionIsThrown()
    {
        $this->expectException(CannotSendEmailException::class);

        $swiftMailer = $this->createMock(\Swift_Mailer::class);
        $swiftMailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(0);

        $mailer = new SwiftMailer($swiftMailer);
        $mailer->send('noreply@adshares.net', 'user@adshares.net', 'subject', 'body');
    }

    public function testSendEmail()
    {
        $swiftMailer = $this->createMock(\Swift_Mailer::class);
        $swiftMailer
            ->expects($this->once())
            ->method('send')
            ->willReturn(1);

        $mailer = new SwiftMailer($swiftMailer);
        $mailer->send('noreply@adshares.net', 'user@adshares.net', 'subject', 'body');
    }
}

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

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Exception\AccountAlreadyExistsException;
use Adshares\AdsOperator\Document\Exception\InvalidEmailException;
use Adshares\AdsOperator\Document\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testChangeEmailWhenEmailIsInvalid()
    {
        $this->expectException(InvalidEmailException::class);

        $email = 'valid@example.com';
        $password = sha1('test');
        $user = new User($email, $password);

        $user->changeEmail('test.example.com');
    }

    public function testChangeEmailWhenValid()
    {
        $email = 'valid@example.com';
        $password = sha1('test');
        $user = new User($email, $password);

        $user->changeEmail('test@example.com');

        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals('test@example.com', $user->getNewEmail());
    }

    public function testConfirmChangeEmail()
    {
        $email = 'valid@example.com';
        $password = sha1('test');
        $user = new User($email, $password);
        $user->changeEmail('test@example.com');

        $this->assertNotNull($user->getNewEmail());
        $this->assertNotNull($user->getToken());

        $user->confirmChangeEmail();

        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertNull($user->getNewEmail());
        $this->assertNull($user->getToken());
    }

    public function testAddAccountWhenDoesNotExist()
    {
        $address = '0001-00000000-9B6F';

        $email = 'valid@example.com';
        $password = sha1('test');
        $user = new User($email, $password);
        $user->addAccount($address);

        $this->assertEquals([$address], $user->getAccounts());
    }

    public function testAddAccountWhenExists()
    {
        $this->expectException(AccountAlreadyExistsException::class);

        $address = '0001-00000000-9B6F';

        $email = 'valid@example.com';
        $password = sha1('test');
        $user = new User($email, $password);
        $user->addAccount($address);
        $user->addAccount($address);

        $this->assertEquals([$address], $user->getAccounts());
    }
}

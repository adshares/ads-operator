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

namespace Adshares\AdsOperator\Document;

use Adshares\AdsOperator\Document\Exception\AccountAlreadyExistsException;
use Adshares\AdsOperator\Document\Exception\InvalidEmailException;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $id;

    private $email;

    private $newEmail;

    private $token;

    private $accounts = [];

    private $password;

    private $createdAt;

    private $updatedAt;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function changeEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException(sprintf('Email %s is not valid.', $email));
        }

        $this->newEmail = $email;
        $this->token = sha1($this->getEmail().time().$this->getId());
    }

    public function confirmChangeEmail()
    {
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->token = null;
    }

    public function isMyAccount(string $address): bool
    {
        return in_array($address, $this->accounts);
    }

    public function addAccount(string $address)
    {
        if (in_array($address, $this->accounts)) {
            throw new AccountAlreadyExistsException(sprintf(
                'Address %s exists and belongs to the user %s',
                $address,
                $this->id
            ));
        }

        $this->accounts[] = $address;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAccounts(): array
    {
        return $this->accounts;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getEmail()
    {
        return $this->getUsername();
    }

    public function getNewEmail()
    {
        return $this->newEmail;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function eraseCredentials()
    {
    }
}

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

namespace Adshares\AdsOperator\Auth;

use Adshares\AdsOperator\Auth\Exception\UserAlreadyExistsException;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\Validator\DocumentValidator;
use Adshares\AdsOperator\Validator\ValidatorException;

class UserRegistration
{
    private $userRepository;

    private $validator;

    public function __construct(UserRepositoryInterface $userRepository, DocumentValidator $validator)
    {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
    }

    public function register(User $user)
    {
        $errors = $this->validator->validate($user);

        if (0 !== count($errors)) {
            throw new ValidatorException($errors);
        }

        if ($this->userRepository->findByEmail($user->getEmail())) {
            throw new UserAlreadyExistsException(sprintf('User %s already exists in the system.', $user->getEmail()));
        }

        $this->userRepository->signUp($user);
    }
}

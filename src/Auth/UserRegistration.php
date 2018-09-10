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

use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\Validator\DocumentValidator;
use Adshares\AdsOperator\Validator\ValidatorException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistration
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var DocumentValidator
     */
    private $validator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(
        UserRepositoryInterface $userRepository,
        DocumentValidator $validator,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param User $user
     */
    public function register(User $user): void
    {
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new ValidatorException($errors);
        }

        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPassword()));
        $this->userRepository->signUp($user);
    }
}

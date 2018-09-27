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

namespace Adshares\AdsOperator\UseCase;

use Adshares\AdsOperator\Auth\PasswordCheckerInterface;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\UseCase\Exception\BadPasswordException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangeUserPassword
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var PasswordCheckerInterface
     */
    private $passwordChecker;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PasswordCheckerInterface $passwordChecker,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->passwordChecker = $passwordChecker;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function change(User $user, string $oldPassword, string $newPassword)
    {
        if (!$this->passwordChecker->isPasswordValid($user, $oldPassword)) {
            throw new BadPasswordException('Given password is invalid.');
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));
        $this->userRepository->save($user);
    }
}

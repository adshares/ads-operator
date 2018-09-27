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

use Adshares\AdsOperator\Repository\Exception\UserNotFoundException;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\UseCase\Exception\BadTokenValueException;
use Adshares\AdsOperator\UseCase\Exception\UserExistsException;
use Psr\Log\LoggerInterface;

class ConfirmChangeUserEmail
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(UserRepositoryInterface $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function confirm(string $id, string $token)
    {
        $user = $this->userRepository->findById($id);

        if ($user->getToken() !== $token) {
            throw new BadTokenValueException(sprintf('Bad token %s for user %s.', $token, $id));
        }

        try {
            $this->userRepository->findByEmail($user->getNewEmail());
        } catch (UserNotFoundException $ex) {
            $user->confirmChangeEmail();
            $this->userRepository->save($user);

            return;
        }

        $this->logger->error(sprintf(
            '[CHANGE EMAIL] Could not change an email. The same email (%s) exists in the database.',
            $user->getNewEmail()
        ));

        throw new UserExistsException(sprintf('Email %s exists in the system.', $user->getNewEmail()));
    }
}

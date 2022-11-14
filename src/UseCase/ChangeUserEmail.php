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

namespace Adshares\AdsOperator\UseCase;

use Adshares\AdsOperator\Auth\PasswordCheckerInterface;
use Adshares\AdsOperator\Document\Exception\InvalidEmailException;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Event\UserChangedEmail;
use Adshares\AdsOperator\Queue\Exception\QueueCannotAddMessage;
use Adshares\AdsOperator\Queue\QueueInterface;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\UseCase\Exception\BadPasswordException;
use Psr\Log\LoggerInterface;

class ChangeUserEmail
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var PasswordCheckerInterface
     */
    private $passwordChecker;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        UserRepositoryInterface $repository,
        QueueInterface $queue,
        PasswordCheckerInterface $passwordChecker,
        LoggerInterface $logger
    ) {
        $this->userRepository = $repository;
        $this->queue = $queue;
        $this->passwordChecker = $passwordChecker;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @param string $newEmail
     * @param string $password
     * @throws InvalidEmailException
     */
    public function change(User $user, string $newEmail, string $password): void
    {
        if (!$this->passwordChecker->isPasswordValid($user, $password)) {
            throw new BadPasswordException('Given password is invalid.');
        }

        $user->changeEmail($newEmail);
        $this->userRepository->save($user);

        $event = new UserChangedEmail($user->getEmail(), $newEmail);

        try {
            $this->queue->publish($event);
        } catch (QueueCannotAddMessage $ex) {
            $context = array_merge(['queue_name' => $event->getName()], $event->toArray());
            $this->logger->error(
                sprintf(
                    '[Queue] Could not add a message to the queue: %s (%s)',
                    $event->getName(),
                    $ex->getMessage()
                ),
                $context
            );
        }
    }
}

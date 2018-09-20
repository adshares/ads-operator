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

namespace Adshares\AdsOperator\Controller\Auth;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Exception\InvalidEmailException;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Repository\Exception\UserNotFoundException;
use Adshares\AdsOperator\UseCase\ChangeUserEmail;
use Adshares\AdsOperator\UseCase\ConfirmChangeUserEmail;
use Adshares\AdsOperator\UseCase\Exception\BadPasswordException;
use Adshares\AdsOperator\UseCase\Exception\BadTokenValueException;
use Adshares\AdsOperator\UseCase\Exception\UserExistsException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends ApiController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var ChangeUserEmail
     */
    private $changeUserEmail;

    private $confirmChangeUserEmail;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ChangeUserEmail $changeUserEmail,
        ConfirmChangeUserEmail $confirmChangeUserEmail
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->changeUserEmail = $changeUserEmail;
        $this->confirmChangeUserEmail = $confirmChangeUserEmail;
    }

    public function changeEmailAction(Request $request, string $id): Response
    {
        $content = (string) $request->getContent();
        $contentDecoded = \GuzzleHttp\json_decode($content, true);

        if (!isset($contentDecoded['email']) || !isset($contentDecoded['password'])) {
            throw new BadRequestHttpException('Email and password are required.');
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new UnauthorizedHttpException('', 'Token does not exist.');
        }

        /** @var User $user */
        $user = $token->getUser();

        if ($user->getId() !== $id) {
            $message = sprintf(
                'User %s does not have permission to modify user: %s',
                $user->getId(),
                $id
            );

            throw new UnauthorizedHttpException('', $message);
        }

        // Check 2FA code when it will be ready

        try {
            $this->changeUserEmail->change($user, $contentDecoded['email'], $contentDecoded['password']);
        } catch (InvalidEmailException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        } catch (BadPasswordException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        }

        return $this->response(null, Response::HTTP_NO_CONTENT);
    }

    public function confirmChangeEmailAction(Request $request, string $id, string $token): Response
    {
        try {
            $this->confirmChangeUserEmail->confirm($id, $token);
        } catch (UserNotFoundException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        } catch (BadTokenValueException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        } catch (UserExistsException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        }

        return $this->response(null, Response::HTTP_NO_CONTENT);
    }
}

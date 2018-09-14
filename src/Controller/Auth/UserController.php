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
use Adshares\AdsOperator\UseCase\ChangeUserEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

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

    /**
     * @var JWTManager
     */
    private $jwtManager;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ChangeUserEmail $changeUserEmail,
        JWTManager $jwtManager
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->changeUserEmail = $changeUserEmail;
        $this->jwtManager = $jwtManager;
    }

    public function changeEmailAction(Request $request, string $id): Response
    {
        $content = (string) $request->getContent();

        try {
            $contentDecoded = \GuzzleHttp\json_decode($content, true);
        } catch (\InvalidArgumentException $ex) {
            throw new \RuntimeException(sprintf('Could not decode given json %s.', $content));
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new UnauthorizedHttpException('Token does not exists.');
        }

        /** @var User $user */
        $user = $token->getUser();

        if ($user->getId() !== $id) {
            throw new UnauthorizedHttpException(sprintf('Does not have permission to modify user: %s', $id));
        }

        // Check 2FA code when it will be ready

        try {
            $this->changeUserEmail->change($user, $contentDecoded['email']);
        } catch (InvalidEmailException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        }

        return $this->arrayResponse(['token' => $this->jwtManager->create($user)]);
    }
}

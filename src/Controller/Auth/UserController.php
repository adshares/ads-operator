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
use Adshares\AdsOperator\Repository\Exception\LocalTransactionNotFoundException;
use Adshares\AdsOperator\Repository\Exception\UserNotFoundException;
use Adshares\AdsOperator\UseCase\ChangeUserEmail;
use Adshares\AdsOperator\UseCase\ChangeUserPassword;
use Adshares\AdsOperator\UseCase\ConfirmChangeUserEmail;
use Adshares\AdsOperator\UseCase\Exception\AddressDoesNotBelongToUserException;
use Adshares\AdsOperator\UseCase\Exception\BadPasswordException;
use Adshares\AdsOperator\UseCase\Exception\BadTokenValueException;
use Adshares\AdsOperator\UseCase\Exception\InvalidValueException;
use Adshares\AdsOperator\UseCase\Exception\TooLowBalanceException;
use Adshares\AdsOperator\UseCase\Exception\UserExistsException;
use Adshares\AdsOperator\UseCase\Transaction\ChangeUserKey;
use Adshares\AdsOperator\UseCase\Transaction\ConfirmChangeUserKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;

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
     * @var ChangeUserPassword
     */
    private $changeUserPassword;

    /**
     * @var ConfirmChangeUserEmail
     */
    private $confirmChangeUserEmail;

    /**
     * @var ChangeUserKey
     */
    private $changeUserKey;

    private $confirmChangeUserKey;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ChangeUserEmail $changeUserEmail,
        ConfirmChangeUserEmail $confirmChangeUserEmail,
        ChangeUserPassword $changeUserPassword,
        ChangeUserKey $changeUserKey,
        ConfirmChangeUserKey $confirmChangeUserKey
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->changeUserEmail = $changeUserEmail;
        $this->confirmChangeUserEmail = $confirmChangeUserEmail;
        $this->changeUserPassword = $changeUserPassword;
        $this->changeUserKey = $changeUserKey;
        $this->confirmChangeUserKey = $confirmChangeUserKey;
    }


    /**
     * @Operation(
     *     summary="Change a user email",
     *     tags={"Auth"},
     *
     *      @SWG\Response(
     *          response=400,
     *          description="Returned when post parameters are invalid"
     *     ),
     *      @SWG\Response(
     *          response=401,
     *          description="Returned when user is unauthorized or does not have permissions"
     *     ),
     *     @SWG\Response(
     *          response=204,
     *          description="Returned when operation is successful",
     *     ),
     *     @SWG\Parameter(
     *          name="",
     *          in="body",
     *          required=true,
     *          description="User data",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="email", type="string"),
     *              @SWG\Property(property="password", type="string")
     *          )
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          type="string",
     *          description="User Id"
     *     )
     * )
     *
     * @param Request $request
     * @return Response
     */
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

    /**
     * @Operation(
     *     summary="Confirm changing an email",
     *     tags={"Auth"},
     *
     *      @SWG\Response(
     *          response=400,
     *          description="Returned when user does not exist or token is invalid or user with 'new_email' exists"
     *     ),
     *     @SWG\Response(
     *          response=204,
     *          description="Returned when operation is successful",
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          type="string",
     *          description="User Id"
     *     ),
     *     @SWG\Parameter(
     *          name="token",
     *          in="path",
     *          type="string",
     *          description="Token"
     *     )
     * )
     *
     * @return Response
     */
    public function confirmChangeEmailAction(string $id, string $token): Response
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

    /**
     * @Operation(
     *     summary="Change a user password",
     *     tags={"Auth"},
     *
     *      @SWG\Response(
     *          response=400,
     *          description="Returned when post parameters are invalid"
     *     ),
     *      @SWG\Response(
     *          response=401,
     *          description="Returned when user is unauthorized or does not have permissions"
     *     ),
     *     @SWG\Response(
     *          response=204,
     *          description="Returned when operation is successful",
     *     ),
     *     @SWG\Parameter(
     *          name="",
     *          in="body",
     *          required=true,
     *          description="User data",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="oldPassword", type="string"),
     *              @SWG\Property(property="password", type="string"),
     *              @SWG\Property(property="confirmedPassword", type="string")
     *          )
     *     )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function changePasswordAction(Request $request): Response
    {
        $content = (string) $request->getContent();
        $contentDecoded = \GuzzleHttp\json_decode($content, true);

        $oldPassword = $contentDecoded['oldPassword'] ?? null;
        $newPassword = $contentDecoded['password'] ?? null;
        $confirmedPassword = $contentDecoded['confirmedPassword'] ?? null;

        if (!$oldPassword || !$newPassword || !$confirmedPassword) {
            throw new BadRequestHttpException('`oldPassword`, `password` and `confirmedPassword` are required.');
        }

        if ($newPassword !== $confirmedPassword) {
            throw new BadRequestHttpException('Passwords are not the same.');
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new UnauthorizedHttpException('', 'Token does not exist.');
        }

        /** @var User $user */
        $user = $token->getUser();

        try {
            $this->changeUserPassword->change($user, $oldPassword, $newPassword);
        } catch (BadPasswordException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        }

        return $this->response(null, Response::HTTP_NO_CONTENT);
    }

    public function changeKeyAction(Request $request): Response
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new UnauthorizedHttpException('', 'Token does not exist.');
        }

        /** @var User $user */
        $user = $token->getUser();

        $content = (string) $request->getContent();
        $contentDecoded = \GuzzleHttp\json_decode($content, true);

        $publicKey = $contentDecoded['publicKey'] ?? '';
        $signature = $contentDecoded['signature'] ?? '';
        $address = $contentDecoded['address'] ?? '';

        try {
            $localTransaction = $this->changeUserKey->change($user, $address, $publicKey, $signature);
        } catch (AddressDoesNotBelongToUserException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        } catch (InvalidValueException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        }

        return $this->response($this->serializer->serialize($localTransaction, 'json'), Response::HTTP_OK);
    }

    public function confirmChangeKeyAction(Request $request): Response
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new UnauthorizedHttpException('', 'Token does not exist.');
        }

        /** @var User $user */
        $user = $token->getUser();

        $content = (string) $request->getContent();
        $contentDecoded = \GuzzleHttp\json_decode($content, true);

        $signature = $contentDecoded['signature'] ?? '';
        $id = $contentDecoded['id'] ?? '';

        try {
            $localTransaction = $this->confirmChangeUserKey->confirm($user, $signature, $id);
        } catch (AddressDoesNotBelongToUserException | InvalidValueException | TooLowBalanceException $ex) {
            throw new BadRequestHttpException($ex->getMessage());
        } catch (LocalTransactionNotFoundException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        }

        return $this->response($this->serializer->serialize($localTransaction, 'json'), Response::HTTP_OK);
    }
}

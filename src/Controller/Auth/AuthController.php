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

use Adshares\AdsOperator\Auth\UserRegistration;
use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Validator\ValidatorException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use JMS\Serializer\DeserializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends ApiController
{
    /**
     * @var UserRegistration
     */
    private $userRegistration;

    public function __construct(UserRegistration $userRegistration)
    {
        $this->userRegistration = $userRegistration;
    }

    /**
     * @Operation(
     *     summary="Register a new user",
     *     tags={"Auth"},
     *
     *      @SWG\Response(
     *          response=400,
     *          description="Returned when post parameters are invalid"
     *     ),
     *     @SWG\Response(
     *          response=204,
     *          description="Returned when operation is successful",
     *      ),
     *     @SWG\Parameter(
     *          name="",
     *          in="body",
     *          required=true,
     *          description="User data",
     *          @SWG\Schema(type="object",
     *              @SWG\Property(property="email", type="string"),
     *              @SWG\Property(property="password", type="string")
     *          )
     *      )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function registerAction(Request $request): Response
    {
        $content = (string) $request->getContent();

        $context = new DeserializationContext();
        $context->setGroups('create');

        /** @var User $user */
        $user = $this->serializer->deserialize($content, User::class, 'json', $context);

        try {
            $this->userRegistration->register($user);
        } catch (ValidatorException $ex) {
            return $this->validationErrorResponse(['errors' => $ex->getErrors()]);
        }

        return $this->response(null, Response::HTTP_CREATED);
    }
}

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

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Transaction;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Repository\AccountRepositoryInterface;
use Adshares\AdsOperator\Repository\TransactionRepositoryInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AccountController extends ApiController
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * AccountController constructor.
     * @param AccountRepositoryInterface $repository
     * @param TransactionRepositoryInterface $transactionRepository
     */
    public function __construct(
        AccountRepositoryInterface $repository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->repository = $repository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @Operation(
     *     summary="List of accounts",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=400,
     *          description="Returned when query parameters are invalid"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Account::class))
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="sort",
     *          in="query",
     *          type="string",
     *          description="The field used to sort accounts"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to set ordering for accounts"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          description="The field used to limit number of accounts"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          description="The field used to specify accounts offset"
     *      )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        return $this->response($this->serializer->serialize($this->getList($request), 'json'), Response::HTTP_OK);
    }


    /**
     * @Operation(
     *     summary="Returns account resource",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Account Id is invalid"
     *     ),
     *      @SWG\Response(
     *          response=404,
     *          description="Returned when Account resource does not exist"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @Model(type=Account::class)
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          type="string",
     *          description="Account Id (hexadecimal number, e.g. 0001-00000000-9B6F)"
     *     )
     * )
     *
     * @param string $id
     * @return Response
     */
    public function showAction(string $id): Response
    {
        $id = strtoupper($id);
        if (!Account::validateId($id)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $account = $this->repository->getAccount($id);

        if (!$account) {
            throw new NotFoundHttpException(sprintf('The requested resource: %s was not found', $id));
        }

        return $this->response($this->serializer->serialize($account, 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="List of transactions for given account",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Account Id is invalid"
     *     ),
     *      @SWG\Response(
     *          response=400,
     *          description="Returned when query parameters are invalid"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Transaction::class))
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="sort",
     *          in="query",
     *          type="string",
     *          description="The field used to sort transactions"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to set ordering for transactions"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          description="The field used to limit number of transactions"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          description="The field used to specify transactions offset"
     *      ),
     *      @SWG\Parameter(
     *          name="accountId",
     *          in="path",
     *          type="string",
     *          description="account Id (hexadecimal number, e.g. 0001-00000001-1234)"
     *      )
     * )
     *
     * @param Request $request
     * @param string $accountId
     * @return Response
     */
    public function transactionsAction(Request $request, string $accountId): Response
    {
        $accountId = strtoupper($accountId);
        if (!Account::validateId($accountId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $this->validateRequest($request, $this->transactionRepository->availableSortingFields());

        $sort = $this->getSort($request);
        $order = $this->getOrder($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $transactions = $this->transactionRepository->getTransactionsByAccountId(
            $accountId,
            $sort,
            $order,
            $limit,
            $offset
        );

        return $this->response($this->serializer->serialize($transactions, 'json'), Response::HTTP_OK);
    }
}

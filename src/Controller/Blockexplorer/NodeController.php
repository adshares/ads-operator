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

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Transaction;
use Adshares\AdsOperator\Repository\AccountRepositoryInterface;
use Adshares\AdsOperator\Repository\MessageRepositoryInterface;
use Adshares\AdsOperator\Repository\NodeRepositoryInterface;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Repository\TransactionRepositoryInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class NodeController extends ApiController
{
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * NodeController constructor.
     * @param NodeRepositoryInterface $repository
     * @param AccountRepositoryInterface $accountRepository
     * @param MessageRepositoryInterface $messageRepository
     * @param TransactionRepositoryInterface $transactionRepository
     */
    public function __construct(
        NodeRepositoryInterface $repository,
        AccountRepositoryInterface $accountRepository,
        MessageRepositoryInterface $messageRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->repository = $repository;
        $this->accountRepository = $accountRepository;
        $this->messageRepository = $messageRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @Operation(
     *     summary="List of nodes",
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
     *              @SWG\Items(ref=@Model(type=Node::class))
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="sort",
     *          in="query",
     *          type="string",
     *          description="The field used to sort nodes"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to set ordering for nodes"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          description="The field used to limit number of nodes"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          description="The field used to specify nodes offset"
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
     *     summary="Returns node resource",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Node Id is invalid"
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Returned when Node resource does not exist"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @Model(type=Node::class)
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          type="string",
     *          description="Node Id (hexadecimal number, e.g. 0001)"
     *     )
     * )
     *
     * @param string $id
     * @return Response
     */
    public function showAction(string $id): Response
    {
        if (!Node::validateId($id)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $node = $this->repository->getNode($id);

        if (!$node) {
            throw new NotFoundHttpException(sprintf('The requested resource: %s was not found', $id));
        }

        return $this->response($this->serializer->serialize($node, 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="Returns accounts resource for given Node",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Node Id is invalid"
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
     *          name="nodeId",
     *          in="path",
     *          type="string",
     *          description="Node Id (hexadecimal number, e.g. 0001)"
     *     ),
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
     * @param string $nodeId
     * @return Response
     */
    public function accountsAction(Request $request, string $nodeId): Response
    {
        if (!Node::validateId($nodeId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $this->validateRequest($request, $this->accountRepository->availableSortingFields());

        $sort = $this->getSort($request);
        $order = $this->getOrder($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $accounts = $this->accountRepository->getAccountsByNodeId(
            $nodeId,
            $sort,
            $order,
            $limit,
            $offset
        );

        return $this->response($this->serializer->serialize($accounts, 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="Returns messages resource for given Node",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Node Id is invalid"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Message::class))
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="nodeId",
     *          in="path",
     *          type="string",
     *          description="Node Id (hexadecimal number, e.g. 0001)"
     *     ),
     *     @SWG\Parameter(
     *          name="sort",
     *          in="query",
     *          type="string",
     *          description="The field used to sort messages"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to set ordering for messages"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          description="The field used to limit number of messages"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          description="The field used to specify messages offset"
     *      )
     * )
     *
     * @param Request $request
     * @param string $nodeId
     * @return Response
     */
    public function messagesAction(Request $request, string $nodeId): Response
    {
        if (!Node::validateId($nodeId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $this->validateRequest($request, $this->messageRepository->availableSortingFields());

        $sort = $this->getSort($request);
        $order = $this->getOrder($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $messages = $this->messageRepository->getMessagesByNodeId(
            $nodeId,
            $sort,
            $order,
            $limit,
            $offset
        );

        return $this->response($this->serializer->serialize($messages, 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="Returns transactons resource for given Node",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Node Id is invalid"
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
     *          name="nodeId",
     *          in="path",
     *          type="string",
     *          description="Node Id (hexadecimal number, e.g. 0001)"
     *     ),
     *      @SWG\Parameter(
     *          name="hideConnections",
     *          in="query",
     *          type="bool",
     *          description="The field used to hide connect transactions"
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
     *      )
     * )
     *
     * @param Request $request
     * @param string $nodeId
     * @return Response
     */
    public function transactionsAction(Request $request, string $nodeId): Response
    {
        if (!Node::validateId($nodeId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $this->validateRequest($request, $this->transactionRepository->availableSortingFields());

        $sort = $this->getSort($request);
        $order = $this->getOrder($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);
        $hideConnections = (bool)$request->get('hideConnections', false);

        $transactions = $this->transactionRepository->getTransactionsByNodeId(
            $nodeId,
            $hideConnections,
            $sort,
            $order,
            $limit,
            $offset
        );

        return $this->response($this->serializer->serialize($transactions, 'json'), Response::HTTP_OK);
    }
}

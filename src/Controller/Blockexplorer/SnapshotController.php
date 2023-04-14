<?php

/**
 * Copyright (c) 2018-2023 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types=1);

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Snapshot;
use Adshares\AdsOperator\Repository\SnapshotAccountRepositoryInterface;
use Adshares\AdsOperator\Repository\SnapshotNodeRepositoryInterface;
use Adshares\AdsOperator\Repository\SnapshotRepositoryInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class SnapshotController extends ApiController
{
    private SnapshotNodeRepositoryInterface $nodeRepository;
    private SnapshotAccountRepositoryInterface $accountRepository;

    public function __construct(
        SnapshotRepositoryInterface $repository,
        SnapshotNodeRepositoryInterface $nodeRepository,
        SnapshotAccountRepositoryInterface $accountRepository
    ) {
        $this->repository = $repository;
        $this->nodeRepository = $nodeRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @Operation(
     *     summary="List of snapshots",
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
     *              @SWG\Items(ref=@Model(type=Snapshot::class))
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="sort",
     *          in="query",
     *          type="string",
     *          description="The field used to sort snapshots"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to set ordering for snapshots"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          description="The field used to limit number of snapshots"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          description="The field used to specify snapshots offset"
     *      )
     * )
     */
    public function listAction(Request $request): Response
    {
        return $this->response($this->serializer->serialize($this->getList($request), 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="Returns snapshot resource",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Snapshot Id is invalid"
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Returned when Snapshot resource does not exist"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @Model(type=Snapshot::class)
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          type="string",
     *          description="Snapshot Id (hexadecimal number, e.g. 63700000)"
     *     )
     * )
     */
    public function showAction(string $id): Response
    {
        $id = strtoupper($id);
        if (!Snapshot::validateId($id)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $snapshot = $this->repository->getSnapshot($id);

        if (!$snapshot) {
            throw new NotFoundHttpException(sprintf('The requested resource: %s was not found', $id));
        }

        return $this->response($this->serializer->serialize($snapshot, 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="Returns nodes resource for given Snapshot",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Snapshot Id is invalid"
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
     *          name="snapshotId",
     *          in="path",
     *          type="string",
     *          description="Snapshot Id (hexadecimal number, e.g. 63700000)"
     *     ),
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
     */
    public function nodesAction(Request $request, string $snapshotId): Response
    {
        $snapshotId = strtoupper($snapshotId);
        if (!Snapshot::validateId($snapshotId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $this->validateRequest($request, $this->nodeRepository->availableSortingFields());

        $sort = $this->getSort($request);
        $order = $this->getOrder($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $accounts = $this->nodeRepository->fetchListBySnapshotId(
            $snapshotId,
            $sort,
            $order,
            $limit,
            $offset
        );

        return $this->response($this->serializer->serialize($accounts, 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="Returns snapshot node resource",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Snapshot Id or Node Id is invalid"
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Returned when Snapshot Node resource does not exist"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @Model(type=Snapshot::class)
     *     ),
     *     @SWG\Parameter(
     *          name="snapshotId",
     *          in="path",
     *          type="string",
     *          description="Snapshot Id (hexadecimal number, e.g. 63700000)"
     *     ),
     *     @SWG\Parameter(
     *          name="nodeId",
     *          in="path",
     *          type="string",
     *          description="Node Id (hexadecimal number, e.g. 0001)"
     *     )
     * )
     */
    public function showNodeAction(string $snapshotId, string $nodeId): Response
    {
        $snapshotId = strtoupper($snapshotId);
        if (!Snapshot::validateId($snapshotId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }
        $nodeId = strtoupper($nodeId);
        if (!Node::validateId($nodeId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $node = $this->nodeRepository->getNode($snapshotId, $nodeId);

        if (!$node) {
            throw new NotFoundHttpException(
                sprintf('The requested resource: %s/%s was not found', $snapshotId, $nodeId)
            );
        }

        return $this->response($this->serializer->serialize($node, 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="Returns accounts resource for given Snapshot",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Snapshot Id is invalid"
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
     *          name="snapshotId",
     *          in="path",
     *          type="string",
     *          description="Snapshot Id (hexadecimal number, e.g. 63700000)"
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
     * @param string $snapshotId
     * @return Response
     */
    public function accountsAction(Request $request, string $snapshotId): Response
    {
        $snapshotId = strtoupper($snapshotId);
        if (!Snapshot::validateId($snapshotId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $this->validateRequest($request, $this->accountRepository->availableSortingFields());

        $sort = $this->getSort($request);
        $order = $this->getOrder($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $accounts = $this->accountRepository->fetchListBySnapshotId(
            $snapshotId,
            $sort,
            $order,
            $limit,
            $offset
        );

        return $this->response($this->serializer->serialize($accounts, 'json'), Response::HTTP_OK);
    }

    /**
     * @Operation(
     *     summary="Returns snapshot account resource",
     *     tags={"Blockexplorer"},
     *
     *      @SWG\Response(
     *          response=422,
     *          description="Returned when Snapshot Id or Account Id is invalid"
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Returned when Snapshot Account resource does not exist"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @Model(type=Snapshot::class)
     *     ),
     *     @SWG\Parameter(
     *          name="snapshotId",
     *          in="path",
     *          type="string",
     *          description="Snapshot Id (hexadecimal number, e.g. 63700000)"
     *     ),
     *     @SWG\Parameter(
     *          name="accountId",
     *          in="path",
     *          type="string",
     *          description="Account Id (hexadecimal number, e.g. 0001-00000000-9B6F)"
     *     )
     * )
     */
    public function showAccountAction(string $snapshotId, string $accountId): Response
    {
        $snapshotId = strtoupper($snapshotId);
        if (!Snapshot::validateId($snapshotId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }
        $accountId = strtoupper($accountId);
        if (!Account::validateId($accountId)) {
            throw new UnprocessableEntityHttpException(self::INVALID_RESOURCE_MESSAGE);
        }

        $account = $this->accountRepository->getAccount($snapshotId, $accountId);

        if (!$account) {
            throw new NotFoundHttpException(
                sprintf('The requested resource: %s/%s was not found', $snapshotId, $accountId)
            );
        }

        return $this->response($this->serializer->serialize($account, 'json'), Response::HTTP_OK);
    }
}

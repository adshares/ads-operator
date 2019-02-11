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
use Adshares\AdsOperator\Document\Info;
use Adshares\AdsOperator\Repository\InfoRepositoryInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InfoController extends ApiController
{
    /**
     * @var int
     */
    protected $genesisTime;

    /**
     * NodeController constructor.
     * @param InfoRepositoryInterface $repository
     * @param int $genesisTime
     */
    public function __construct(InfoRepositoryInterface $repository, int $genesisTime) {
        $this->repository = $repository;
        $this->genesisTime = $genesisTime;
    }

    /**
     * @Operation(
     *     summary="Coin info",
     *     tags={"coin"},
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
     *              @SWG\Items(ref=@Model(type=Info::class))
     *          )
     *      ),
     * )
     *
     * @return Response
     */
    public function showAction(): Response
    {
        $info = $this->repository->getInfo($this->genesisTime);

        if (!$info) {
            throw new NotFoundHttpException(sprintf('The requested resource was not found'));
        }

        return $this->response($this->serializer->serialize($info, 'json'), Response::HTTP_OK);
    }
}

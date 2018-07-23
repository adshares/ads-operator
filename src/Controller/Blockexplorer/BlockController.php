<?php

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Block;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;

class BlockController extends ApiController
{
    /**
     * @Operation(
     *     summary="List of blocks",
     *     tags={"Block"},
     *
     *      @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Block::class))
     *          )
     *      )
     * )
     */
    public function listAction()
    {
        return $this->response($this->serializer->serialize([], 'json'));
    }

    /**
     *
     * @Operation(
     *     summary="Get block details",
     *     tags={"Block"},
     *
     *      @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @Model(type=Block::class)
     *      ),
     *
     *      @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          type="string",
     *          description="Block identity"
     *      )
     * )
     */
    public function showAction()
    {
        return $this->response($this->serializer->serialize(new Block('1234'), 'json'));
    }
}

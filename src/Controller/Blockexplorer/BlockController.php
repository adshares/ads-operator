<?php

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Block;
use JMS\Serializer\DeserializationContext;
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
}

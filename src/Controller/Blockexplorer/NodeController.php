<?php

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Node;
use JMS\Serializer\DeserializationContext;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;

class NodeController extends ApiController
{
    /**
     * @Operation(
     *     summary="List of nodes",
     *     tags={"Node"},
     *
     *      @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Node::class))
     *          )
     *      )
     * )
     */
    public function listAction()
    {

        $content = [
            "id" => "0F4A:1111:FDSA",
            "balance" => 123123,
        ];

        $context = new DeserializationContext();
//        $this->serializer->deserialize(json_encode($content), Node::class, 'json', $context);

        $node = new Node('123');
        return $this->response($this->serializer->serialize([$node, $node, $node], 'json'));
    }
}

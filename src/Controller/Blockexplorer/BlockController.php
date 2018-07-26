<?php

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Repository\BlockRepositoryInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends ApiController
{
    /**
     * BlockController constructor.
     * @param BlockRepositoryInterface $repository
     */
    public function __construct(BlockRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Operation(
     *     summary="List of blocks",
     *     tags={"Blockexplorer"},
     *
     *     @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Block::class))
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="sort",
     *          in="query",
     *          type="string",
     *          description="The field used to order blocks"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to sort blocks"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="int",
     *          description="The field used to limit number of blocks"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="int",
     *          description="The field used to specify blocks offset"
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
}

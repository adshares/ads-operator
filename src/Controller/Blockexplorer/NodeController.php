<?php

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Repository\NodeRepositoryInterface;
use Adshares\AdsOperator\Document\Node;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NodeController extends ApiController
{
    /**
     * @var NodeRepositoryInterface
     */
    private $repository;

    /**
     * NodeController constructor.
     * @param NodeRepositoryInterface $repository
     */
    public function __construct(NodeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Operation(
     *     summary="List of nodes",
     *     tags={"Node"},
     *
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
     *          description="The field used to order nodes"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to sort nodes"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="int",
     *          description="The field used to limit number of nodes"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="int",
     *          description="The field used to specify nodes offset"
     *      )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $this->validateRequest($request, $this->repository->availableSortingFields());

        $sort = $this->getSort($request);
        $order = $this->getOrder($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $nodes = $this->repository->findNodes($sort, $order, $limit, $offset);

        return $this->response($this->serializer->serialize($nodes, 'json'), Response::HTTP_OK);
    }
}

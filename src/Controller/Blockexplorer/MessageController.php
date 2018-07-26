<?php

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Repository\MessageRepositoryInterface;
use Adshares\AdsOperator\Document\Message;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends ApiController
{
    /**
     * BlockController constructor.
     * @param MessageRepositoryInterface $repository
     */
    public function __construct(MessageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Operation(
     *     summary="List of messages",
     *     tags={"Blockexplorer"},
     *
     *     @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful",
     *          @SWG\Schema(
     *              type="array",
     *              @SWG\Items(ref=@Model(type=Message::class))
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="sort",
     *          in="query",
     *          type="string",
     *          description="The field used to order messages"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to sort messages"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="int",
     *          description="The field used to limit number of messages"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="int",
     *          description="The field used to specify messages offset"
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

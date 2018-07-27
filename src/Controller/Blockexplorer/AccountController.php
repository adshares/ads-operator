<?php

namespace Adshares\AdsOperator\Controller\Blockexplorer;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Repository\AccountRepositoryInterface;
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
     * AccountController constructor.
     * @param AccountRepositoryInterface $repository
     */
    public function __construct(AccountRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Operation(
     *     summary="List of accounts",
     *     tags={"Blockexplorer"},
     *
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
     *          description="The field used to order accounts"
     *      ),
     *      @SWG\Parameter(
     *          name="order",
     *          in="query",
     *          type="string",
     *          description="The field used to sort accounts"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="int",
     *          description="The field used to limit number of accounts"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="int",
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
     *          response=200,
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
        if (!Account::validateId($id)) {
            throw new UnprocessableEntityHttpException('Invalid resource identity');
        }

        $account = $this->repository->getAccount($id);

        if (!$account) {
            throw new NotFoundHttpException(sprintf('The requested resource: %s was not found', $id));
        }

        return $this->response($this->serializer->serialize($account, 'json'), Response::HTTP_OK);
    }
}

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
use Adshares\AdsOperator\Document\Stats\TransactionTicker;
use Adshares\AdsOperator\Repository\TickerRepositoryInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StatsController extends ApiController
{
    /**
     * @var int
     */
    protected $defaultLimit = 100;

    /**
     * @var int
     */
    protected $maxLimit = 500;

    /**
     * @var string
     */
    protected $defaultInterval = 'day';

    /**
     * @var TickerRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * NodeController constructor.
     * @param TickerRepositoryInterface $transactionRepository
     * @param int $genesisTime
     */
    public function __construct(TickerRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @Operation(
     *     summary="Transactions stats",
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
     *              @SWG\Items(ref=@Model(type=TransactionTicker::class))
     *          )
     *      ),
     *     @SWG\Parameter(
     *          name="start",
     *          in="query",
     *          type="string",
     *          description="The field used to limit tickers"
     *      ),
     *     @SWG\Parameter(
     *          name="end",
     *          in="query",
     *          type="string",
     *          description="The field used to limit tickers"
     *      ),
     *      @SWG\Parameter(
     *          name="interval",
     *          in="query",
     *          type="string",
     *          description="The field used to specify aggregation"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          in="query",
     *          type="integer",
     *          description="The field used to limit number of tickers"
     *      ),
     *      @SWG\Parameter(
     *          name="offset",
     *          in="query",
     *          type="integer",
     *          description="The field used to specify tickers offset"
     *      )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function transactionsAction(Request $request): Response
    {
        $this->validateRequest($request, $this->transactionRepository->availableTickerIntervals());

        $start = $this->getStart($request);
        $end = $this->getEnd($request);
        $interval = $this->getInterval($request);
        $limit = $this->getLimit($request);
        $offset = $this->getOffset($request);

        $tickers = $this->transactionRepository->getTickers(
            $start,
            $end,
            $interval,
            $limit,
            $offset
        );

        return $this->response($this->serializer->serialize($tickers, 'json'), Response::HTTP_OK);
    }

    protected function validateRequest(Request $request, array $availableIntervals): void
    {
        $interval = $this->normalizeInterval($request->get('interval'));
        $start = $request->get('start');
        $end = $request->get('end');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        if ($interval !== null && (!is_string($interval) || !in_array($interval, $availableIntervals))) {
            throw new BadRequestHttpException(sprintf(
                'Interval value `%s` is invalid. Only %s values are supported.',
                $interval,
                implode(', ', $availableIntervals)
            ));
        }

        if ($start !== null && strtotime($start) === false) {
            throw new BadRequestHttpException(sprintf(
                'Start date `%s` is invalid.',
                $start
            ));
        }

        if ($end !== null && strtotime($end) === false) {
            throw new BadRequestHttpException(sprintf(
                'End date `%s` is invalid.',
                $end
            ));
        }

        if ($offset !== null && (!is_numeric($offset) || ($offset < 0 || $offset > $this->maxOffset))) {
            throw new BadRequestHttpException(sprintf(
                'Offset value `%s` is invalid. Value must be between %s and %s.',
                $offset,
                0,
                $this->maxOffset
            ));
        }

        if ($limit !== null && (!is_numeric($limit) || ($limit < 1 || $limit > $this->maxLimit))) {
            throw new BadRequestHttpException(sprintf(
                'Limit value `%s` is invalid. Value must be between %s and %s.',
                $limit,
                1,
                $this->maxLimit
            ));
        }
    }

    /**
     * @param null|string $snake
     * @return null|string
     */
    private function normalizeInterval(?string $snake): ?string
    {
        if (!$snake) {
            return null;
        }

        return lcfirst(str_replace('_', '', ucwords($snake, '_')));
    }

    /**
     * @param Request $request
     * @return \DateTime
     */
    protected function getStart(Request $request): \DateTime
    {
        $start = $request->get('start');

        if ($start === null) {
            $start = (new \DateTime())->setTime(0, 0)->sub(new \DateInterval('P30D'));
        } else {
            $start = new \DateTime($start);
        }

        return $start;
    }

    /**
     * @param Request $request
     * @return \DateTime
     */
    protected function getEnd(Request $request): \DateTime
    {
        $end = $request->get('end');

        if ($end === null) {
            $end = (new \DateTime())->setTime(0, 0);
        } else {
            $end = new \DateTime($end);
        }

        return $end;
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getInterval(Request $request): string
    {
        $interval = $this->normalizeInterval($request->get('interval'));

        if ($interval === null) {
            $interval = $this->defaultInterval;
        }

        return $interval;
    }
}

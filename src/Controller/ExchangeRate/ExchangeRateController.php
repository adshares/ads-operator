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

declare(strict_types=1);

namespace Adshares\AdsOperator\Controller\ExchangeRate;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Repository\Exception\ExchangeRateNotFoundException;
use Adshares\AdsOperator\Repository\ExchangeRateRepositoryInterface;
use DateTime;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function sprintf;

class ExchangeRateController extends ApiController
{
    /** @var ExchangeRateRepositoryInterface */
    protected $repository;

    public function __construct(ExchangeRateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Operation(
     *     summary="Returns an exchange rate value",
     *     tags={"ExchangeRate"},
     *
     *      @SWG\Response(
     *          response=400,
     *          description="Returned when path parameters are invalid"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Returned when operation is successful"
     *      ),
     *      @SWG\Parameter(
     *          name="date",
     *          in="path",
     *          type="string",
     *          description="Date (ISO8601 format)"
     *      ),
     *      @SWG\Parameter(
     *          name="currency",
     *          in="path",
     *          type="string",
     *          description="Exchange rate currency"
     *      )
     * )
     */
    public function showAction(string $date, string $currency): Response
    {
        $currency = strtolower($currency);
        $hourlyDate = DateTime::createFromFormat(DateTime::ATOM, $date);

        if (!$hourlyDate) {
            throw new BadRequestHttpException(sprintf('Date (%s) is not valid.', $date));
        }

        if ($currency !== 'usd' && $currency !== 'btc') {
            throw new BadRequestHttpException('Only `usd` and `btc` currencies are supported.');
        }

        $hourlyDate->setTime((int)$hourlyDate->format('H'), 0);

        try {
            $exchangeRate = $this->repository->fetch($hourlyDate, $currency);
        } catch (ExchangeRateNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }

        return $this->response($this->serializer->serialize($exchangeRate, 'json'));
    }
}

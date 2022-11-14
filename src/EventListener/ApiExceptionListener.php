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

namespace Adshares\AdsOperator\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * ApiExceptionListener listens for every exception and transform it as a JSON response.
 * @package Adshares\AdsOperator\EventListener
 */
class ApiExceptionListener
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ApiExceptionListener constructor.
     * @param string $environment
     * @param LoggerInterface $logger
     */
    public function __construct(string $environment, LoggerInterface $logger)
    {
        $this->environment = $environment;
        $this->logger = $logger;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $data = $this->prepareResponse($exception->getStatusCode(), $exception->getMessage());

            $response = new JsonResponse($data, $exception->getStatusCode());
        } else {
            $data = $this->prepareResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal server error');

            if (in_array($this->environment, ['dev', 'test'])) {
                $data['dev'] = $this->getDevBlock($exception);
            }

            $previousData = [];
            $previousException = $exception->getPrevious();

            if ($previousException) {
                $previousData = [
                    'message' => $previousException->getMessage(),
                    'code' => $previousException->getCode(),
                    'line' => $previousException->getLine(),
                    'file' => $previousException->getFile(),
                ];
            }

            $this->logger->error(
                $exception->getMessage(),
                [
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'previous' => $previousData,
                ]
            );

            $response = new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }

    /**
     * @param int $code
     * @param string $message
     * @return array
     */
    private function prepareResponse(int $code, string $message): array
    {
        return [
            'code' => $code,
            'message' => $message,
        ];
    }

    /**
     * @param \Throwable $exception
     * @return array
     */
    private function getDevBlock(\Throwable $exception): array
    {
        return [
            'message' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString(),
        ];
    }
}

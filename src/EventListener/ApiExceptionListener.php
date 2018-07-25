<?php

namespace Adshares\AdsOperator\EventListener;

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
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse(
                [
                    'code' => $exception->getStatusCode(),
                    'message' => $exception->getMessage(),
                ],
                $exception->getStatusCode()
            );
        } else {
            $response = new JsonResponse(
                [
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'Internal server error',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $event->setResponse($response);
    }
}

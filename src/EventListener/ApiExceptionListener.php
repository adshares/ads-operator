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
     * @var string
     */
    private $environment;

    /**
     * ApiExceptionListener constructor.
     * @param string $environment
     */
    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $data = [
                'code' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ];

            $response = new JsonResponse(
                $data,
                $exception->getStatusCode()
            );
        } else {
            $data = [
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Internal server error',
            ];

            if ($this->environment === 'dev') {
                $data['dev'] = [
                    'message' => $exception->getMessage(),
                    'stack' => $exception->getTraceAsString(),
                ];
            }

            $response = new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}

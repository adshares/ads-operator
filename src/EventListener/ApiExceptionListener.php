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
            $data = $this->prepareResponse($exception->getStatusCode(), $exception->getMessage());

            $response = new JsonResponse($data, $exception->getStatusCode());
        } else {
            $data = $this->prepareResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal server error');

            if ($this->environment === 'dev') {
                $data['dev'] = $this->getDevBlock($exception);
            }

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

<?php

namespace Adshares\AdsOperator\Controller;

use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected function response($data = null, int $status = JsonResponse::HTTP_OK, array $headers = []): Response
    {
        $headers = array_merge($headers, ['content-type' => 'application/json']);

        return new Response($data, $status, $headers);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }
}

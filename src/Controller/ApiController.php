<?php

namespace Adshares\AdsOperator\Controller;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected function response($data = null, int $status = JsonResponse::HTTP_OK, array $headers = []): JsonResponse
    {
        $headers = array_merge($headers, ['content-type' => 'application/json']);

        return new JsonResponse($data, $status, $headers);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }
}

<?php

namespace Adshares\AdsOperator\Controller;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected function response($data = null, int $status = Response::HTTP_OK, array $headers = []): Response
    {
        $headers = array_merge($headers, ['content-type' => 'application/json']);

        return new Response($data, $status, $headers);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }
}

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

namespace Adshares\AdsOperator\Tests\Unit\Controller;

use Adshares\AdsOperator\Controller\ApiController;
use Adshares\AdsOperator\Tests\Unit\PrivateMethodTrait;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ApiControllerTest extends TestCase
{
    use PrivateMethodTrait;

    public function testValidateRequestWhenSortIsInvalid(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $availableSortingFields = ['id', 'title'];
        $request = $this->getRequestMock(['sort' => 'example-sort']);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, $availableSortingFields]);
    }

    public function testValidateRequestWhenSortIsValid(): void
    {
        $availableSortingFields = ['id', 'title'];
        $request = $this->getRequestMock(['sort' => 'id']);
        $apiController = new ApiController();
        $result = $this->invokeMethod($apiController, 'validateRequest', [$request, $availableSortingFields]);

        $this->assertNull($result);
    }

    public function testValidateRequestWhenOrderIsInvalid(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $request = $this->getRequestMock(['order' => 'example-order']);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
    }

    public function testValidateRequestWhenOrderIsValid(): void
    {
        foreach (['desc', 'DESC', 'asc', 'ASC'] as $order) {
            $request = $this->getRequestMock(['order' => $order]);
            $apiController = new ApiController();
            $result = $this->invokeMethod($apiController, 'validateRequest', [$request, []]);

            $this->assertNull($result);
        }
    }

    public function testValidateRequestWhenLimitIsInvalid(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $request = $this->getRequestMock(['limit' => -10]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);

        $this->expectException(BadRequestHttpException::class);

        $request = $this->getRequestMock(['limit' => 0]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);

        $this->expectException(BadRequestHttpException::class);

        $request = $this->getRequestMock(['limit' => 1000]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
    }

    public function testValidateRequestWhenLimitIsValid(): void
    {
        $request = $this->getRequestMock(['limit' => 100]);
        $apiController = new ApiController();
        $result = $this->invokeMethod($apiController, 'validateRequest', [$request, []]);

        $this->assertNull($result);
    }

    public function testValidateRequestWhenOffsetIsInvalid(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $request = $this->getRequestMock(['offset' => -10]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);

        $this->expectException(BadRequestHttpException::class);

        $request = $this->getRequestMock(['offset' => 600]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
    }

    public function testValidateRequestWhenOffsetIsValid(): void
    {
        $request = $this->getRequestMock(['offset' => 10]);
        $apiController = new ApiController();
        $result = $this->invokeMethod($apiController, 'validateRequest', [$request, []]);

        $this->assertNull($result);
    }

    public function testGetLimitWhenIsNotSetByUser(): void
    {
        $request = $this->getRequestMock([]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
        $limit = $this->invokeMethod($apiController, 'getLimit', [$request]);
        $this->assertEquals(25, $limit);
    }

    public function testGetOffsetWhenIsNotSetByUser(): void
    {
        $request = $this->getRequestMock([]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
        $offset = $this->invokeMethod($apiController, 'getOffset', [$request]);
        $this->assertEquals(0, $offset);
    }

    public function testGetLimitWhenUserGivesCorrectValue(): void
    {
        $request = $this->getRequestMock(['limit' => 51]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
        $limit = $this->invokeMethod($apiController, 'getLimit', [$request]);
        $this->assertEquals(51, $limit);
    }

    public function testGetOffsetWhenUserGivesCorrectValue(): void
    {
        $request = $this->getRequestMock(['offset' => 100]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
        $offset = $this->invokeMethod($apiController, 'getOffset', [$request]);
        $this->assertEquals(100, $offset);
    }

    public function testGetSortWhenIsNotSetByUser(): void
    {
        $request = $this->getRequestMock([]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
        $sort = $this->invokeMethod($apiController, 'getSort', [$request]);
        $this->assertEquals('id', $sort);
    }

    public function testGetOrderWhenIsNotSetByUser(): void
    {
        $request = $this->getRequestMock([]);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
        $order = $this->invokeMethod($apiController, 'getOrder', [$request]);
        $this->assertEquals('desc', $order);
    }

    public function testGetSortWhenUserGivesCorrectValue(): void
    {
        $request = $this->getRequestMock(['sort' => 'example-field']);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, ['example-field']]);
        $sort = $this->invokeMethod($apiController, 'getSort', [$request]);
        $this->assertEquals('example-field', $sort);
    }

    public function testGetOrderWhenUserGivesCorrectValue(): void
    {
        $request = $this->getRequestMock(['order' => 'desc']);
        $apiController = new ApiController();
        $this->invokeMethod($apiController, 'validateRequest', [$request, []]);
        $order = $this->invokeMethod($apiController, 'getOrder', [$request]);
        $this->assertEquals('desc', $order);
    }

    public function testResponse(): void
    {
        $headers = ['custom-header' => 'custom'];
        $data = json_encode(
            [
                'one' => 'one one',
                'two' => 'two two',
            ]
        );

        $apiController = new ApiController();
        /** @var Response $result */
        $result = $this->invokeMethod($apiController, 'response', [$data, 204, $headers]);

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(204, $result->getStatusCode());
        $this->assertEquals($data, $result->getContent());
        $this->assertEquals('custom', $result->headers->get('custom-header'));
        $this->assertEquals('application/json', $result->headers->get('content-type'));
    }

    public function testSetSerialize(): void
    {
        $serializationMock = $this->createMock(SerializerInterface::class);
        $apiController = new ApiController();
        $result = $this->invokeMethod($apiController, 'setSerializer', [$serializationMock]);

        $this->assertNull($result);
    }

    public function testNormalizeSortWhenValueIsNull()
    {
        $apiController = new ApiController();
        $result = $this->invokeMethod($apiController, 'normalizeSort', [null]);

        $this->assertNull($result);
    }

    public function testNormalizeSortWhenValueIsNotNull()
    {
        $value = 'one_two_three';
        $expected = 'oneTwoThree';
        $apiController = new ApiController();
        $result = $this->invokeMethod($apiController, 'normalizeSort', [$value]);

        $this->assertEquals($expected, $result);
    }


    private function getRequestMock(array $input)
    {
        $returnValues = array_merge(
            [
                'sort' => null,
                'order' => null,
                'limit' => null,
                'offset' => null,
            ],
            $input
        );

        $map = [
            ['sort', null, $returnValues['sort']],
            ['order', null, $returnValues['order']],
            ['limit', null, $returnValues['limit']],
            ['offset', null, $returnValues['offset']],
        ];

        $request = $this->createMock(Request::class);
        $request
            ->method('get')
            ->will($this->returnValueMap($map));


        return $request;
    }
}

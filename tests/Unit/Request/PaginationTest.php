<?php

namespace Adshares\AdsOperator\Tests\Unit\Request;

use Adshares\AdsOperator\Request\Pagination;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class PaginationTest extends TestCase
{
    /**
     * @dataProvider getDataForPagination
     *
     * @param array $params
     */
    public function testPagination(array $params, $sort, $order, $limit, $offset): void
    {
        $availableSortFields = ['id', 'balance'];

        $request = $this->createMock(Request::class);
        $request
            ->method('get')
            ->will($this->returnValueMap($params));

        $pagination = new Pagination($request, $availableSortFields);

        $this->assertEquals($limit, $pagination->getLimit());
        $this->assertEquals($offset, $pagination->getOffset());
        $this->assertEquals($sort, $pagination->getSort());
        $this->assertEquals($order, $pagination->getOrder());
    }

    public function getDataForPagination(): array
    {
        return [
            [
                [
                    ['sort', null, null],
                    ['order', null, null],
                    ['limit', null, null],
                    ['offset', null, null],
                ],
                'id',
                'desc',
                null,
                null,
            ],
            [
                [
                    ['sort', null, 'unknown_field'],
                    ['order', null, 'asc'],
                    ['limit', null, 10],
                    ['offset', null, 10],
                ],
                'id',
                'asc',
                10,
                10,
            ],
            [
                [
                    ['sort', null, 'balance'],
                    ['order', null, 'asc'],
                    ['limit', null, -100],
                    ['offset', null, -10],
                ],
                'balance',
                'asc',
                0,
                0,
            ],
            [
                [
                    ['sort', null, 'balance'],
                    ['order', null, 'asc'],
                    ['limit', null, 'not-supported-value'],
                    ['offset', null, 'not-supported-value'],
                ],
                'balance',
                'asc',
                0,
                0,
            ]
        ];
    }
}

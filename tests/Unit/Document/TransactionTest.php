<?php

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testValidation()
    {
        $ids = [
            "1234:12341234:1234" => true,
            "A234:12341234:1230" => true,
            "A234-12341234-1230" => false,
            "1234:1234" => false,
            "1234" => false,
        ];

        foreach ($ids as $id => $expected) {
            $this->assertEquals($expected, Transaction::validateId((string)$id));
        }
    }
}

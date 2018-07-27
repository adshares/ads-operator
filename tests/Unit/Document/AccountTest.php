<?php

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testValidation()
    {
        $ids = [
            "1234:1234:1234" => false,
            "1234-1234-1234" => false,
            "1234:1234" => false,
            "1234" => false,
            "1234-1234AFGA-12DD" => true,
        ];

        foreach ($ids as $id => $expected) {
            $this->assertEquals($expected, Account::validateId((string)$id));
        }
    }
}

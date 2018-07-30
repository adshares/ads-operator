<?php

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testMessage(): void
    {
        $transactionCount = 10;
        $message = new Message($transactionCount);

        $this->assertEquals($transactionCount, $message->getTransactionCount());
    }

    public function testValidation()
    {
        $ids = [
            "1234:12341234" => true,
            "1234:123412345" => false,
            "1234-12341234" => false,
            "1234-1234-1234" => false,
            "1234:1234" => false,
            "1234" => false,
        ];

        foreach ($ids as $id => $expected) {
            $this->assertEquals($expected, Message::validateId((string)$id));
        }
    }
}

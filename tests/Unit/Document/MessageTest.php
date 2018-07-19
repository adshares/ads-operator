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
}

<?php

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Node;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    public function testBlock(): void
    {
        $id = '00001';
        $nodes = [
            new Node('1'),
            new Node('2'),
            new Node('3'),
            new Node('4'),
        ];
        $messageCount = 5;

        $block = new Block($id, $nodes, $messageCount);

        $this->assertEquals($id, $block->getId());
        $this->assertEquals($nodes, $block->getNodes());
        $this->assertEquals($messageCount, $block->getMessageCount());

        $block->setTransactionCount(100);
        $this->assertEquals(100, $block->getTransactionCount());
    }

    public function testValidation()
    {
        $ids = [
            "12341234" => true,
            "123AF234" => true,
            "F2341230" => true,
            "F23412302" => false,
            "1234-1234-1234" => false,
            "1234:1234" => false,
            "1234" => false,
        ];

        foreach ($ids as $id => $expected) {
            $this->assertEquals($expected, Block::validateId((string)$id));
        }
    }
}

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
}

<?php

namespace Adshares\AdsOperator\Tests\Unit\Document;

use Adshares\AdsOperator\Document\Node;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testValidation()
    {
        $ids = [
            "1234" => true,
            "A2G4" => true,
            "A2G40" => false,
            "1234-1234-1234" => false,
            "1234:1234" => false,
            "1234-1234AFGA-12DD" => false,
        ];

        foreach ($ids as $id => $expected) {
            $this->assertEquals($expected, Node::validateId((string)$id));
        }
    }
}

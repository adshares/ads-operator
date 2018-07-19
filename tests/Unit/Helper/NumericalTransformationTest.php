<?php

namespace Adshares\AdsOperator\Tests\Unit\Helper;

use Adshares\AdsOperator\Helper\NumericalTransformation;
use PHPUnit\Framework\TestCase;

final class NumericalTransformationTest extends TestCase
{
    public function testHexToDec(): void
    {
        $this->assertEquals(15, NumericalTransformation::hexToDec('000f'));
    }

    public function testDecToHex(): void
    {
        $this->assertEquals('000F', NumericalTransformation::decToHex(15));
    }
}

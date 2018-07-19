<?php


namespace Adshares\AdsOperator\Helper;

class NumericalTransformation
{
    public static function hexToDec(string $hex): int
    {
        return hexdec($hex);
    }

    public static function decToHex(int $dec): string
    {
        return sprintf("%04X", $dec);
    }
}

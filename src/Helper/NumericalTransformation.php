<?php


namespace Adshares\AdsManager\Helper;

class NumericalTransformation
{
    public static function hexToDec(string $hex): int
    {
        return hexdec($hex);
    }

    public static function decToHex(int $dec): string
    {
        return dechex($dec);
    }
}

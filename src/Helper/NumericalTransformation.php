<?php


namespace Adshares\AdsOperator\Helper;

/**
 * Numerical transformations.
 *
 * @package Adshares\AdsOperator\Helper
 */
class NumericalTransformation
{
    /**
     * @param string $hex
     * @return int
     */
    public static function hexToDec(string $hex): int
    {
        return hexdec($hex);
    }

    /**
     * @param int $dec
     * @return string
     */
    public static function decToHex(int $dec): string
    {
        return sprintf("%04X", $dec);
    }
}

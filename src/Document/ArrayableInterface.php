<?php

namespace Adshares\AdsOperator\Document;

/**
 * Interface provides method for returning object as an array.
 * @package Adshares\AdsOperator\Document
 */
interface ArrayableInterface
{
    /**
     * @return array
     */
    public function toArray(): array;
}

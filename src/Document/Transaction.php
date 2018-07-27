<?php

namespace Adshares\AdsOperator\Document;

class Transaction
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $id
     * @return bool
     */
    public static function validateId(string $id): bool
    {
        return (bool) preg_match('/^[0-9A-Z]{4}:[0-9A-Z]{8}:[0-9A-Z]{4}$/', $id);
    }
}

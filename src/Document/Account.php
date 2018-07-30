<?php

namespace Adshares\AdsOperator\Document;

/**
 * Class Account
 * @package Adshares\AdsOperator\Document
 */
class Account extends \Adshares\Ads\Entity\Account
{
    protected $id;

    /**
     * @param string $id
     * @return bool
     */
    public static function validateId(string $id): bool
    {
        return (bool) preg_match('/^[0-9A-Z]{4}-[0-9A-Z]{8}-[0-9A-Z]{4}$/', $id);
    }
}

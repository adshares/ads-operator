<?php

namespace Adshares\AdsOperator\Document;

/**
 * Class Node
 * @package Adshares\AdsOperator\Document
 */
class Node extends \Adshares\Ads\Entity\Node
{
    const SPECIAL_NODE = '0000';

    /**
     * Node constructor.
     * @param string|null $id
     */
    public function __construct(string $id = null)
    {
        if (null !== $id) {
            $this->id = $id;
        }
    }

    /**
     * @return bool
     */
    public function isSpecial(): bool
    {
        return $this->getId() === self::SPECIAL_NODE;
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function validateId(string $id): bool
    {
        return (bool) preg_match('/^[0-9A-Z]{4}$/', $id);
    }
}

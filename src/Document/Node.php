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
}

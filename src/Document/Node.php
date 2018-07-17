<?php


namespace Adshares\AdsOperator\Document;

class Node extends \Adshares\Ads\Entity\Node
{
    const SPECIAL_NODE = '0000';

    public function __construct($id = null)
    {
        if ($id) {
            $this->id = $id;
        }
    }

    public function isSpecial(): bool
    {
        return $this->getId() === self::SPECIAL_NODE;
    }
}

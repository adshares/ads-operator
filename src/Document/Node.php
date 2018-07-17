<?php


namespace Adshares\AdsOperator\Document;

class Node extends \Adshares\Ads\Entity\Node
{
    public function __construct($id = null)
    {
        if ($id) {
            $this->id = $id;
        }
    }
}

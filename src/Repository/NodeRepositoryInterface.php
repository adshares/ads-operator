<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Node;

/**
 * Interface NodeRepositoryInterface
 * @package Adshares\AdsOperator\Repository
 */
interface NodeRepositoryInterface extends ListRepositoryInterface
{
    /**
     * @param string $nodeId
     * @return Node
     */
    public function getNode(string $nodeId):? Node;
}

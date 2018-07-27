<?php

namespace Adshares\AdsOperator\Repository;

use Adshares\AdsOperator\Document\Message;

/**
 * Interface MessageRepositoryInterface
 * @package Adshares\AdsOperator\Repository
 */
interface MessageRepositoryInterface extends ListRepositoryInterface
{
    /**
     * @param string $messageId
     * @return Message
     */
    public function getMessage(string $messageId):? Message;
}

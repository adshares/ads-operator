<?php

namespace Adshares\AdsOperator\Repository\Doctrine;

use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Repository\MessageRepositoryInterface;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    /**
     * @return array
     */
    public function availableSortingFields(): array
    {
        return [
            'id',
            'blockId',
        ];
    }

    /**
     * @param string $messageId
     * @return Message|null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getMessage(string $messageId):? Message
    {
        /** @var Message $message */
        $message = $this->find($messageId);

        return $message;
    }
}

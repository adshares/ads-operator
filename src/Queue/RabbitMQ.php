<?php
/**
 * Copyright (C) 2018 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator.  If not, see <https://www.gnu.org/licenses/>
 */

namespace Adshares\AdsOperator\Queue;

use Adshares\AdsOperator\Event\EventInterface;
use Adshares\AdsOperator\Queue\Exception\QueueCannotAddMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements QueueInterface
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param EventInterface $event
     * @throws QueueCannotAddMessage
     * @return mixed
     */
    public function publish(EventInterface $event)
    {
        $queueName = $event->getName();

        try {
            $queueMessage = \GuzzleHttp\json_encode($event->toArray());
        } catch (\InvalidArgumentException $ex) {
            throw new QueueCannotAddMessage('There was a problem with json encoding.');
        }

        $message = new AMQPMessage($queueMessage, ['delivery_mode' => 2]);

        $channel = $this->createChannel();
        $channel->queue_declare($queueName, false, true, false, false);
        $channel->basic_publish($message, '', $queueName);

        $channel->close();
        $this->connection->close();
    }

    private function createChannel(): AMQPChannel
    {
        try {
            return $this->connection->channel();
        } catch (AMQPProtocolConnectionException $ex) {
            throw new QueueCannotAddMessage($ex->getMessage());
        }
    }
}

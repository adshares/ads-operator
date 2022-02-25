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

namespace Adshares\AdsOperator\Tests\Unit\Queue;

use Adshares\AdsOperator\Event\EventInterface;
use Adshares\AdsOperator\Queue\Exception\QueueCannotAddMessage;
use Adshares\AdsOperator\Queue\RabbitMQ;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;
use PHPUnit\Framework\TestCase;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQTest extends TestCase
{
    public function testPublishWhenPositiveScenario()
    {
        $data = [
            'old_email' => 'old@example.com',
            'new_email' => 'new@example.com',
        ];

        $channel = $this->createMock(AMQPChannel::class);
        $channel
            ->expects($this->once())
            ->method('queue_declare');
        $channel
            ->expects($this->once())
            ->method('basic_publish');

        $amqpConnection = $this->createMock(AMQPStreamConnection::class);
        $amqpConnection
            ->expects($this->once())
            ->method('channel')
            ->willReturn($channel);

        $event = $this->createMock(EventInterface::class);
        $event
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Event #1');

        $event
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($data);

        (new RabbitMQ($amqpConnection))->publish($event);
    }

    public function testPublishWhenQueueMessageCannotBeCreated()
    {
        $this->expectException(QueueCannotAddMessage::class);

        $data = [
            'old_email' => 'old@example.com',
            'new_email' => 'new@example.com',
        ];

        $amqpConnection = $this->createMock(AMQPStreamConnection::class);
        $amqpConnection
            ->expects($this->once())
            ->method('channel')
            ->will($this->throwException(new AMQPProtocolConnectionException(
                '530',
                530,
                [10, 40]
            )));

        $event = $this->createMock(EventInterface::class);
        $event
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Event #1');

        $event
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($data);

        (new RabbitMQ($amqpConnection))->publish($event);
    }
}

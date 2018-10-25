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

namespace Adshares\AdsOperator\Tests\Unit\EventListener;

use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\EventListener\ApiExceptionListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ApiExceptionListenerTest extends TestCase
{
    public function testApiExceptionListenerWhenHttpExceptionInterfaceIsThrown(): void
    {
        $exception = $this->createMock(BadRequestHttpException::class);
        $exception
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(400);

        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event
            ->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

        $listener = new ApiExceptionListener('prod', new NullLogger());
        $listener->onKernelException($event);
    }

    public function testApiExceptionListenerWhenExceptionInternalErrorOccurs(): void
    {
        $exception = $this->createMock(\Exception::class);

        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event
            ->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

        $listener = new ApiExceptionListener('prod', new NullLogger());
        $listener->onKernelException($event);
    }

    public function testApiExceptionListenerWhenExceptionInternalErrorOccursAndDevEnvironment(): void
    {
        $exception = $this->createMock(AdsClientException::class);

        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event
            ->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

        $listener = new ApiExceptionListener('dev', new NullLogger());
        $listener->onKernelException($event);
    }
}

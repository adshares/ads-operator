<?php

namespace Adshares\AdsOperator\Tests\Unit\EventListener;

use Adshares\AdsOperator\AdsImporter\Exception\AdsClientException;
use Adshares\AdsOperator\EventListener\ApiExceptionListener;
use PHPUnit\Framework\TestCase;
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

        $listener = new ApiExceptionListener('prod');
        $listener->onKernelException($event);
    }

    public function testApiExceptionListenerWhenExceptionInternalErrorOccurs(): void
    {
        $exception = $this->createMock(\Exception::class);
        $exception
            ->expects($this->exactly(0))
            ->method('getMessage');

        $event = $this->createMock(GetResponseForExceptionEvent::class);
        $event
            ->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

        $listener = new ApiExceptionListener('prod');
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

        $listener = new ApiExceptionListener('dev');
        $listener->onKernelException($event);
    }
}

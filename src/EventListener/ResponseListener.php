<?php

/**
 * Copyright (c) 2018-2023 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types=1);

namespace Adshares\AdsOperator\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * ResponseListener listens for every response and add custom headers.
 * @package Adshares\AdsOperator\EventListener
 */
class ResponseListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ApiExceptionListener constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('X-Robots-Tag', 'noindex');
    }
}

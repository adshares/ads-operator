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

namespace Adshares\AdsOperator\Command;

use Adshares\AdsOperator\Event\UserChangedEmail;
use Adshares\AdsOperator\Queue\QueueInterface;
use Adshares\AdsOperator\UseCase\SendChangeUserEmailConfirmation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendChangeEmailConfirmationCommand extends ContainerAwareCommand
{
    private $queue;

    private $sendEmail;

    public function __construct(QueueInterface $queue, SendChangeUserEmailConfirmation $sendEmail)
    {
        $this->queue = $queue;
        $this->sendEmail = $sendEmail;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('consumer:change-email-confirmation')
            ->setDescription('Send a confirmation email containing a link');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $callback = function ($message) {
            $data = json_decode($message->body, true);

            $this->sendEmail->send($data['new_email']);
        };

        $this->queue->consume(UserChangedEmail::EVENT_NAME, $callback);
    }
}

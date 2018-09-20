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

namespace Adshares\AdsOperator\UseCase;

use Adshares\AdsOperator\Mailer\Exception\CannotSendEmailException;
use Adshares\AdsOperator\Mailer\MailerInterface;
use Adshares\AdsOperator\Repository\Exception\UserNotFoundException;
use Adshares\AdsOperator\Repository\UserRepositoryInterface;
use Adshares\AdsOperator\Template\Exception\CannotFindTemplateException;
use Adshares\AdsOperator\Template\TemplateInterface;
use Psr\Log\LoggerInterface;

class SendChangeUserEmailConfirmation
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var MailerInterface
     */
    private $emailProvider;

    /**
     * @var TemplateInterface
     */
    private $template;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * @var string
     */
    private $senderEmail;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        UserRepositoryInterface $userRepository,
        MailerInterface $emailProvider,
        TemplateInterface $template,
        string $templatePath,
        string $senderEmail,
        string $subject,
        string $url,
        LoggerInterface $logger
    ) {
        $this->userRepository = $userRepository;
        $this->emailProvider = $emailProvider;
        $this->template = $template;
        $this->templatePath = $templatePath;
        $this->senderEmail = $senderEmail;
        $this->subject = $subject;
        $this->baseUrl = $url;
        $this->logger = $logger;
    }

    public function send(string $email)
    {
        try {
            $user = $this->userRepository->findByNewEmail($email);
        } catch (UserNotFoundException $exception) {
            $this->addExceptionToLog($email, $exception);
            return;
        }

        $url = sprintf('/auth/users/%s/confirm-change-email/%s', $user->getId(), $user->getToken());
        $params = [
            'url' => $this->baseUrl.$url,
        ];

        try {
            $body = $this->template->render($this->templatePath, $params);
            $this->emailProvider->send($this->senderEmail, $email, $this->subject, $body);
        } catch (CannotFindTemplateException $exception) {
            $this->addExceptionToLog($email, $exception);
        } catch (CannotSendEmailException $exception) {
            $this->addExceptionToLog($email, $exception);
        }
    }

    private function addExceptionToLog(string $email, \Exception $exception)
    {
        $this->logger->error(sprintf(
            '[CHANGE EMAIL] Could not sent a confirmation email to %s (%s)',
            $email,
            $exception->getMessage()
        ));
    }
}

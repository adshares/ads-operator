<?php
/**
 * Copyright (C) 2018 Adshares sp. z. o.o.
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

namespace Adshares\AdsOperator\Tests\Unit\Validator;

use Adshares\AdsOperator\Document\User;
use Adshares\AdsOperator\Validator\DocumentValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentValidatorTest extends TestCase
{
    public function testWhenValidatorHasNoErrors()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->method('validate')
            ->willReturn([]);

        $documentValidator = new DocumentValidator($validator);
        $this->assertEquals([], $documentValidator->validate(new User('test@adshares.net', sha1('test'))));
    }

    public function testWhenValidatorContainsErrorsForDifferentFields()
    {
        $error1 = $this->createMock(ConstraintViolation::class);
        $error2 = $this->createMock(ConstraintViolation::class);

        $error1
            ->method('getPropertyPath')
            ->willReturn('email');

        $error1
            ->method('getMessage')
            ->willReturn('message #1');

        $error2
            ->method('getPropertyPath')
            ->willReturn('username');

        $error2
            ->method('getMessage')
            ->willReturn('message #2');


        $expected = [
            'email' => [
                'message #1',
            ],
            'username' => [
                'message #2',
            ]
        ];

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->method('validate')
            ->willReturn([$error1, $error2]);

        $documentValidator = new DocumentValidator($validator);
        $this->assertEquals($expected, $documentValidator->validate(new User('test@adshares.net', sha1('test'))));
    }

    public function testWhenValidatorContainsErrorsForTheSameField()
    {
        $error1 = $this->createMock(ConstraintViolation::class);
        $error2 = $this->createMock(ConstraintViolation::class);

        $error1
            ->method('getPropertyPath')
            ->willReturn('email');

        $error1
            ->method('getMessage')
            ->willReturn('message #1');

        $error2
            ->method('getPropertyPath')
            ->willReturn('email');

        $error2
            ->method('getMessage')
            ->willReturn('message #2');


        $expected = [
            'email' => [
                'message #1',
                'message #2',
            ],
        ];

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->method('validate')
            ->willReturn([$error1, $error2]);

        $documentValidator = new DocumentValidator($validator);
        $this->assertEquals($expected, $documentValidator->validate(new User('test@adshares.net', sha1('test'))));
    }
}

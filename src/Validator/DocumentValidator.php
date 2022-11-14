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

declare(strict_types=1);

namespace Adshares\AdsOperator\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentValidator implements DocumentValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param mixed $document document object (e.g. User, Transaction)
     * @return array
     */
    public function validate($document): array
    {
        $result = $this->validator->validate($document);

        if (0 === count($result)) {
            return [];
        }

        $errors = [];

        foreach ($result as $error) {
            $errors[$error->getPropertyPath()][] = $error->getMessage();
        }

        return $errors;
    }
}

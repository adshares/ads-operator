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

namespace Adshares\AdsOperator\Document;

/**
 * Class Node
 * @package Adshares\AdsOperator\Document
 */
class Node extends \Adshares\Ads\Entity\Node
{
    const SPECIAL_NODE = '0000';

    /**
     * @var string
     */
    protected $version = 'n/a';

    /**
     * Node constructor.
     * @param string|null $id
     */
    public function __construct(string $id = null)
    {
        if (null !== $id) {
            $this->id = $id;
        }
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function isSpecial(): bool
    {
        return $this->getId() === self::SPECIAL_NODE;
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function validateId(string $id): bool
    {
        return (bool) preg_match('/^[0-9A-F]{4}$/', $id);
    }
}

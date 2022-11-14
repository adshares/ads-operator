<?php
/**
 * Copyright (c) 2018-2022 Adshares sp. z o.o.
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

namespace Adshares\AdsOperator\Repository\Doctrine;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;

/**
 * BaseRepository provides common methods used in all repositories.
 * @package Adshares\AdsOperator\Repository\Doctrine
 */
class BaseRepository extends DocumentRepository
{
    /**
     * @param string $sort
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param array $conditions
     * @return array
     */
    public function fetchList(string $sort, string $order, int $limit, int $offset, array $conditions = []): array
    {
        $results = [];
        $count = 0;

        try {
            $cursor = $this
                ->createQueryBuilder()
                ->sort($sort, $order)
                ->limit($limit)
                ->skip($offset);

            if ($conditions) {
                foreach ($conditions as $columnName => $value) {
                    $cursor->field($columnName)->equals($value);
                }
            }

            $cursor = $cursor
                ->getQuery()
                ->execute();
            $data = $cursor->toArray();
            $count = $cursor->count();

            foreach ($data as $item) {
                $results[] = $item;
            }
        } catch (MongoDBException $ex) {
        }

        return [
            'data' => $results,
            'meta' => ['count' => $count]
        ];
    }
}

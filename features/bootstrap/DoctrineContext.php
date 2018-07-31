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

use Adshares\AdsOperator\Document\Node;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ODM\MongoDB\DocumentManager;

class DoctrineContext implements Context
{
    /**
     * @var DocumentManager
     */
    private $documentManger;

    public function __construct(DocumentManager $documentManger)
    {
        $this->documentManger = $documentManger;

        $database = $this->documentManger->getDocumentDatabase(Node::class);
        $database->drop();
    }

    /**
     * @Given nodes exist in application:
     *
     * @param TableNode $table
     */
    public function nodesExistInApplication(TableNode $table): void
    {
        foreach ($table->getHash() as $data) {
            $node = Node::createFromRawData($data);
            $this->documentManger->persist($node);
        }

        $this->documentManger->flush();
    }
}

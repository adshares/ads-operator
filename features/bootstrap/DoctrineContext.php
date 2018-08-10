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

use Adshares\AdsOperator\Document\Account;
use Adshares\AdsOperator\Document\Block;
use Adshares\AdsOperator\Document\Message;
use Adshares\AdsOperator\Document\Node;
use Adshares\AdsOperator\Document\Transaction\BroadcastTransaction;
use Adshares\AdsOperator\Document\Transaction\ConnectionTransaction;
use Adshares\AdsOperator\Document\Transaction\EmptyTransaction;
use Adshares\AdsOperator\Document\Transaction\KeyTransaction;
use Adshares\AdsOperator\Document\Transaction\LogAccountTransaction;
use Adshares\AdsOperator\Document\Transaction\SendOneTransaction;
use Adshares\AdsOperator\Document\Transaction\SendManyTransaction;
use Adshares\AdsOperator\Document\Transaction\StatusTransaction;
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
     * @Given :entity exist in application:
     *
     * @param string $entity
     * @param TableNode $table
     *
     * @throws Exception
     */
    public function entitesExistInApplication(string $entity, TableNode $table): void
    {
        $map = [
            'accounts' => Account::class,
            'blocks' => Block::class,
            'messages' => Message::class,
            'nodes' => Node::class,
            'broadcastTransaction' => BroadcastTransaction::class,
            'connectionTransaction' => ConnectionTransaction::class,
            'emptyTransaction' => EmptyTransaction::class,
            'keyTransaction' => KeyTransaction::class,
            'logAccountTransaction' => LogAccountTransaction::class,
            'sendOneTransaction' => SendOneTransaction::class,
            'sendManyTransaction' => SendManyTransaction::class,
            'statusTransaction' => StatusTransaction::class,
        ];

        if (!isset($map[$entity])) {
            throw new Exception('Does not exist');
        }

        /** @var \Adshares\Ads\Entity\EntityInterface $class */
        $class = $map[$entity];

        foreach ($table->getHash() as $data) {
            $node = $class::createFromRawData($data);
            $this->documentManger->persist($node);
        }
        $this->documentManger->flush();
    }

}

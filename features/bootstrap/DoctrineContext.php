<?php

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

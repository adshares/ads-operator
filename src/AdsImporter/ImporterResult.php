<?php


namespace Adshares\AdsOperator\AdsImporter;

/**
 * Stores information how many objects have been recently imported.
 *
 * @package Adshares\AdsOperator\AdsImporter
 */
class ImporterResult
{
    public $blocks = 0;
    public $messages = 0;
    public $transactions = 0;
    public $nodes = 0;
    public $accounts = 0;
}

<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector;

use PHP_CodeSniffer\Sniffs\Sniff;
use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Sniff\Naming\SniffNaming;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor\SniffPropertyValuesExtractor;

final class SniffPropertyValueDataCollector
{
    /**
     * @var SniffPropertyValuesExtractor
     */
    private $sniffPropertyValuesExtractor;

    /**
     * @var array[]
     */
    private $sniffPropertyValuesBySniffClass = [];

    public function __construct(SniffPropertyValuesExtractor $sniffPropertyValuesExtractor)
    {
        $this->sniffPropertyValuesExtractor = $sniffPropertyValuesExtractor;
    }

    public function collectFromRuleXmlElement(SimpleXMLElement $ruleXmlElement)
    {
        if (!isset($ruleXmlElement->properties)) {
            return;
        }

        $sniffCode = (string) $ruleXmlElement['ref'];
        $sniffClass = SniffNaming::guessClassByCode($sniffCode);

        $properties = $this->sniffPropertyValuesExtractor->extractFromRuleXmlElement($ruleXmlElement);
        $this->addSniffPropertyValues($sniffClass, $properties);
    }

    public function getForSniff(Sniff $sniff) : array
    {
        $sniffClass = get_class($sniff);
        if (!isset($this->sniffPropertyValuesBySniffClass[$sniffClass])) {
            return [];
        }

        return $this->sniffPropertyValuesBySniffClass[$sniffClass];
    }

    private function addSniffPropertyValues(string $sniffCode, array $propertyValues)
    {
        if (!isset($this->sniffPropertyValuesBySniffClass[$sniffCode])) {
            $this->sniffPropertyValuesBySniffClass[$sniffCode] = [];
        }

        $this->sniffPropertyValuesBySniffClass[$sniffCode] = array_merge(
            $this->sniffPropertyValuesBySniffClass[$sniffCode],
            $propertyValues
        );
    }
}

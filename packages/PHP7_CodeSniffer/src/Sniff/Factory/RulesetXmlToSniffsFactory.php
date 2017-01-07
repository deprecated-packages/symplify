<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\SniffSetFactoryAwareInterface;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Sniff\Naming\SniffNaming;
use Symplify\PHP7_CodeSniffer\Sniff\SniffSetFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Sorter\SniffSorter;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\SniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;

final class RulesetXmlToSniffsFactory implements
    SniffFactoryInterface,
    SniffSetFactoryAwareInterface
{
    /**
     * @var ExcludedSniffDataCollector
     */
    private $excludedSniffDataCollector;

    /**
     * @var SniffPropertyValueDataCollector
     */
    private $customSniffPropertyDataCollector;

    /**
     * @var SniffSetFactory
     */
    private $sniffSetFactory;

    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var SingleSniffFactory
     */
    private $singleSniffFactory;

    public function __construct(
        SniffFinder $sniffFinder,
        ExcludedSniffDataCollector $excludedSniffDataCollector,
        SniffPropertyValueDataCollector $customSniffPropertyDataCollector,
        SingleSniffFactory $singleSniffFactory
    ) {
        $this->sniffFinder = $sniffFinder;
        $this->customSniffPropertyDataCollector = $customSniffPropertyDataCollector;
        $this->excludedSniffDataCollector = $excludedSniffDataCollector;
        $this->singleSniffFactory = $singleSniffFactory;
    }

    public function isMatch(string $reference) : bool
    {
        return Strings::endsWith($reference, 'ruleset.xml');
    }

    /**
     * @return Sniff[]
     */
    public function create(string $rulesetXmlFile) : array
    {
        $sniffs = $this->createSniffsFromOwnRuleset($rulesetXmlFile);

        $rulesetXml = simplexml_load_file($rulesetXmlFile);
        foreach ($rulesetXml->rule as $ruleXmlElement) {
            if ($this->isRuleXmlElementSkipped($ruleXmlElement)) {
                continue;
            }

            $this->excludedSniffDataCollector->collectFromRuleXmlElement($ruleXmlElement);
            $this->customSniffPropertyDataCollector->collectFromRuleXmlElement($ruleXmlElement);

            $sniffs = array_merge($sniffs, $this->sniffSetFactory->create($ruleXmlElement['ref']));
        }

        return SniffSorter::sort($sniffs);
    }

    public function setSniffSetFactory(SniffSetFactory $sniffSetFactory)
    {
        $this->sniffSetFactory = $sniffSetFactory;
    }

    private function isRuleXmlElementSkipped(SimpleXMLElement $ruleXmlElement) : bool
    {
        if (!isset($ruleXmlElement['ref'])) {
            return true;
        }

        if (isset($ruleXmlElement->severity)) {
            if (SniffNaming::isSniffCode($ruleXmlElement['ref'])) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @return Sniff[]
     */
    private function createSniffsFromOwnRuleset(string $rulesetXmlFile) : array
    {
        $rulesetDir = dirname($rulesetXmlFile);
        $sniffDir = $rulesetDir.DIRECTORY_SEPARATOR.'Sniffs';
        if (!is_dir($sniffDir)) {
            return [];
        }

        $sniffClassNames = $this->sniffFinder->findAllSniffClassesInDirectory($sniffDir);

        $sniffs = [];
        foreach ($sniffClassNames as $sniffClassName) {
            if ($sniff = $this->singleSniffFactory->create($sniffClassName)) {
                $sniffs[] = $sniff;
            }
        }

        return $sniffs;
    }
}

<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\SniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;

final class SingleSniffFactory
{
    /**
     * @var ExcludedSniffDataCollector
     */
    private $excludedSniffDataCollector;

    /**
     * @var SniffPropertyValueDataCollector
     */
    private $sniffPropertyValueDataCollector;

    public function __construct(
        ExcludedSniffDataCollector $excludedSniffDataCollector,
        SniffPropertyValueDataCollector $customSniffPropertyDataCollector
    ) {
        $this->excludedSniffDataCollector = $excludedSniffDataCollector;
        $this->sniffPropertyValueDataCollector = $customSniffPropertyDataCollector;
    }

    /**
     * @return Sniff|null
     */
    public function create(string $sniffClassName)
    {
        if ($this->excludedSniffDataCollector->isSniffClassExcluded($sniffClassName)) {
            return null;
        }

        $sniff = new $sniffClassName;
        return $this->setCustomSniffPropertyValues($sniff);
    }

    private function setCustomSniffPropertyValues(Sniff $sniff) : Sniff
    {
        $sniffPropertyValues = $this->sniffPropertyValueDataCollector->getForSniff($sniff);
        foreach ($sniffPropertyValues as $property => $value) {
            $sniff->$property = $value;
        }

        return $sniff;
    }
}

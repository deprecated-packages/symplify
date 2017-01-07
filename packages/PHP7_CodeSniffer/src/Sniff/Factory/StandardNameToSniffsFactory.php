<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class StandardNameToSniffsFactory implements SniffFactoryInterface
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    /**
     * @var RulesetXmlToSniffsFactory
     */
    private $rulesetXmlToSniffsFactory;

    public function __construct(
        StandardFinder $standardFinder,
        RulesetXmlToSniffsFactory $rulesetXmlToSniffsFactory
    ) {
        $this->standardFinder = $standardFinder;
        $this->rulesetXmlToSniffsFactory = $rulesetXmlToSniffsFactory;
    }

    public function isMatch(string $reference) : bool
    {
        $standards = $this->standardFinder->getStandards();
        return (isset($standards[$reference]));
    }

    public function create(string $standardName) : array
    {
        $rulesetXml = $this->standardFinder->getRulesetPathForStandardName($standardName);
        return $this->rulesetXmlToSniffsFactory->create($rulesetXml);
    }
}

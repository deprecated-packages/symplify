<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\SniffSetFactoryAwareInterface;
use Symplify\PHP7_CodeSniffer\Sniff\Sorter\SniffSorter;

final class SniffSetFactory
{
    /**
     * @var SniffFactoryInterface[]
     */
    private $sniffFactories;

    public function addSniffFactory(SniffFactoryInterface $sniffFactory)
    {
        $this->sniffFactories[] = $sniffFactory;
        if ($sniffFactory instanceof SniffSetFactoryAwareInterface) {
            $sniffFactory->setSniffSetFactory($this);
        }
    }

    /**
     * @param string[] $standardNames
     * @param string[] $sniffCodes
     * @return Sniff[]
     */
    public function createFromStandardsAndSniffs(
        array $standardNames,
        array $sniffCodes
    ) : array {
        $sniffs = array_merge(
            $this->create($standardNames),
            $this->create($sniffCodes)
        );

        return SniffSorter::sort($sniffs);
    }

    /**
     * @param string|array $source
     * @return Sniff[]
     */
    public function create($source) : array
    {
        $sources = $this->toArray($source);

        $sniffs = [];
        foreach ($this->sniffFactories as $sniffFactory) {
            foreach ($sources as $source) {
                if ($sniffFactory->isMatch($source)) {
                    $sniffs = array_merge($sniffs, $sniffFactory->create($source));
                }
            }
        }

        return $sniffs;
    }

    /**
     * @param string|array $source
     */
    private function toArray($source) : array
    {
        if (is_array($source)) {
            return $source;
        }

        return [$source];
    }
}

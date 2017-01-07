<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Sorter;

use PHP_CodeSniffer\Sniffs\Sniff;

final class SniffSorter
{
    /**
     * @param Sniff[] $sniffs
     * @return Sniff[]
     */
    public static function sort(array $sniffs) : array
    {
        usort($sniffs, function ($oneSniff, $otherSniff) {
            return strcmp(
                get_class($oneSniff),
                get_class($otherSniff)
            );
        });

        return $sniffs;
    }
}

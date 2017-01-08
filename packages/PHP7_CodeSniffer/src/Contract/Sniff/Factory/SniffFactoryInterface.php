<?php

declare(strict_types = 1);

namespace Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;

interface SniffFactoryInterface
{
    public function isMatch(string $reference) : bool;

    /**
     * @param string $standardName
     * @return Sniff[]
     */
    public function create(string $standardName) : array;
}

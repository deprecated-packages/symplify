<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

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

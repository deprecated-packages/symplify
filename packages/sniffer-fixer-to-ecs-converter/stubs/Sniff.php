<?php

declare(strict_types=1);

namespace PHP_CodeSniffer\Sniffs;

use PHP_CodeSniffer\Files\File;

if (interface_exists(Sniff::class)) {
    return;
}

interface Sniff
{
    /**
     * @return array<int|string>
     */
    public function register();

    /**
     * @param int $stackPtr
     * @return void|int
     */
    public function process(File $phpcsFile, $stackPtr);
}

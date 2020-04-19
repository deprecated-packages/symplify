<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use Symplify\CodingStandard\TokenRunner\Guard\TokenTypeGuard;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\SnifferWrapper\SniffClassWrapper;

final class SniffClassWrapperFactory
{
    /**
     * @var Naming
     */
    private $naming;

    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    public function __construct(Naming $naming, TokenTypeGuard $tokenTypeGuard)
    {
        $this->naming = $naming;
        $this->tokenTypeGuard = $tokenTypeGuard;
    }

    public function createFromFirstClassInFile(File $file): ?SniffClassWrapper
    {
        $possibleClassPosition = $file->findNext(T_CLASS, 0);
        if (! is_int($possibleClassPosition)) {
            return null;
        }

        $this->tokenTypeGuard->ensureIsTokenType($file->getTokens()[$possibleClassPosition], [
            T_CLASS,
            T_TRAIT,
            T_INTERFACE,
        ], __METHOD__);

        return new SniffClassWrapper($file, $possibleClassPosition, $this->naming);
    }
}

<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ClassWrapperFactory
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

    public function createFromFirstClassInFile(File $file): ?ClassWrapper
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

        return new ClassWrapper($file, $possibleClassPosition, $this->naming);
    }
}

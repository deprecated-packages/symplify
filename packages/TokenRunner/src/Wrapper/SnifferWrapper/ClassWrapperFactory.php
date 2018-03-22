<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\SnifferWrapper;

use PHP_CodeSniffer\Files\File;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ClassWrapperFactory
{
    public static function createFromFirstClassInFile(File $file): ?ClassWrapper
    {
        $possibleClassPosition = $file->findNext(T_CLASS, 0);
        if (! is_int($possibleClassPosition)) {
            return null;
        }

        TokenTypeGuard::ensureIsTokenType($file->getTokens()[$possibleClassPosition], [
            T_CLASS,
            T_TRAIT,
            T_INTERFACE,
        ], __METHOD__);

        return new ClassWrapper($file, $possibleClassPosition);
    }
}

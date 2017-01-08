<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Helper;

use PHP_CodeSniffer_File;

/**
 * Inspired by https://github.com/Zenify/CodingStandard/blob/071a296bca199c8b7341e7c9e2f20f33d81b230e/src/ZenifyCodingStandard/Helper/Commenting/FunctionHelper.php
 */
final class FunctionHelper
{
    public static function isAbstract(PHP_CodeSniffer_File $codeSnifferFile, int $functionPointer): bool
    {
        return ! isset($codeSnifferFile->getTokens()[$functionPointer]['scope_opener']);
    }

    public static function findReturnTypeHint(PHP_CodeSniffer_File $codeSnifferFile, int $functionPointer)
    {
        $tokens = $codeSnifferFile->getTokens();
        $isAbstract = self::isAbstract($codeSnifferFile, $functionPointer);
        $colonToken = $isAbstract
            ? $codeSnifferFile->findNext(
                [T_COLON, T_INLINE_ELSE],
                $tokens[$functionPointer]['parenthesis_closer'] + 1,
                null,
                false,
                null,
                true
            )
            : $codeSnifferFile->findNext(
                [T_COLON, T_INLINE_ELSE],
                $tokens[$functionPointer]['parenthesis_closer'] + 1,
                $tokens[$functionPointer]['scope_opener'] - 1
            );

        if ($colonToken === false) {
            return;
        }

        $returnTypeHint = null;
        $nextToken = $colonToken;
        do {
            $nextToken = $isAbstract
                ? $codeSnifferFile->findNext([T_WHITESPACE, T_COMMENT, T_SEMICOLON], $nextToken + 1, null, true, null, true)
                : $codeSnifferFile->findNext(
                    [T_WHITESPACE, T_COMMENT],
                    $nextToken + 1,
                    $tokens[$functionPointer]['scope_opener'] - 1,
                    true
                );
            $isTypeHint = $nextToken !== false;
            if ($isTypeHint) {
                $returnTypeHint .= $tokens[$nextToken]['content'];
            }
        } while ($isTypeHint);

        return $returnTypeHint;
    }
}

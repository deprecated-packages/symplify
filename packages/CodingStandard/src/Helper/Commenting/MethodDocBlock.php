<?php declare(strict_types = 1);

namespace Symplify\CodingStandard\Helper\Commenting;

use PHP_CodeSniffer_File;

final class MethodDocBlock
{
    public static function hasMethodDocBlock(PHP_CodeSniffer_File $file, int $position) : bool
    {
        $tokens = $file->getTokens();
        $currentToken = $tokens[$position];
        $docBlockClosePosition = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $position);

        if ($docBlockClosePosition === false) {
            return false;
        }

        $docBlockCloseToken = $tokens[$docBlockClosePosition];
        if ($docBlockCloseToken['line'] === ($currentToken['line'] - 1)) {
            return true;
        }

        return false;
    }

    public static function getMethodDocBlock(PHP_CodeSniffer_File $file, int $position) : string
    {
        if (! self::hasMethodDocBlock($file, $position)) {
            return '';
        }

        $commentStart = $file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $position - 1);
        $commentEnd = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $position - 1);
        return $file->getTokensAsString($commentStart, $commentEnd - $commentStart + 1);
    }
}

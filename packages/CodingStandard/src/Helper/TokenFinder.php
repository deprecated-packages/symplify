<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Helper;

use PHP_CodeSniffer\Files\File;

/**
 * Inspired by https://github.com/slevomat/coding-standard/blob/master/SlevomatCodingStandard/Helpers/TokenHelper.php
 */
final class TokenFinder
{
    /**
     * @var int[]
     */
    public static $ineffectiveTokenCodes = [
        T_WHITESPACE,
        T_COMMENT,
        T_DOC_COMMENT,
        T_DOC_COMMENT_OPEN_TAG,
        T_DOC_COMMENT_CLOSE_TAG,
        T_DOC_COMMENT_STAR,
        T_DOC_COMMENT_STRING,
        T_DOC_COMMENT_TAG,
        T_DOC_COMMENT_WHITESPACE,
    ];

    /**
     * @return int|null
     */
    public static function findPreviousEffective(
        File $phpcsFile, int $startPointer, int $endPointer = null
    ) {
        return self::findPreviousExcluding($phpcsFile, self::$ineffectiveTokenCodes, $startPointer, $endPointer);
    }

    /**
     * @return int|null
     */
    public static function findPreviousExcluding(
        File $phpcsFile, array $types, int $startPointer, int $endPointer = null
    ) {
        $token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, true);
        if ($token === false) {
            return;
        }
        return $token;
    }

    /**
     * @return int|null
     */
    public static function findNextEffective(File $phpcsFile, int $startPointer, int $endPointer = null)
    {
        $token = $phpcsFile->findNext(self::$ineffectiveTokenCodes, $startPointer, $endPointer, true);
        if ($token === false) {
            return;
        }

        return $token;
    }

    public static function findNextLinePosition(File $file, int $position) : int
    {
        $tokens = $file->getTokens();
        $currentLine = $tokens[$position]['line'];
        $nextLinePosition = $position;
        do {
            ++$nextLinePosition;
            $nextLine = $tokens[$nextLinePosition]['line'];
        } while ($currentLine === $nextLine);

        return $nextLinePosition;
    }

    public static function findAllOfType(File $file, int $type, int $start, int $end) : array
    {
        $result = [];

        $currentPosition = $start;
        while (($parameterPosition = $file->findNext($type, $currentPosition, $end)) !== false) {
            $currentPosition = $parameterPosition + 1;
            $result[] = $parameterPosition;
        }

        return $result;
    }
}

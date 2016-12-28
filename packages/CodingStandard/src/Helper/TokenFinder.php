<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\CodingStandard\Helper;

use PHP_CodeSniffer_File;

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
        PHP_CodeSniffer_File $phpcsFile, int $startPointer, int $endPointer = null
    ) {
        return self::findPreviousExcluding($phpcsFile, self::$ineffectiveTokenCodes, $startPointer, $endPointer);
    }

    /**
     * @return int|null
     */
    public static function findPreviousExcluding(
        PHP_CodeSniffer_File $phpcsFile, array $types, int $startPointer, int $endPointer = null
    ) {
        $token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, true);
        if ($token === false) {
            return null;
        }
        return $token;
    }

    /**
     * @return int|null
     */
    public static function findNextEffective(PHP_CodeSniffer_File $phpcsFile, int $startPointer, int $endPointer = null)
    {
        $token = $phpcsFile->findNext(self::$ineffectiveTokenCodes, $startPointer, $endPointer, true);
        if ($token === false) {
            return null;
        }

        return $token;
    }

    public static function findNextLinePosition(PHP_CodeSniffer_File $file, int $position) : int
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

    public static function findAllOfType(PHP_CodeSniffer_File $file, int $type, int $start, int $end) : array
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

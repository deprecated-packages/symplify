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
final class ContentFinder
{
    public static function getContentBetween(PHP_CodeSniffer_File $file, int $startPosition, int $endPosition) : string
    {
        $tokens = self::getTokensBetween($file, $startPosition, $endPosition);

        return implode('', $tokens);
    }

    public static function getTokensBetween(PHP_CodeSniffer_File $file, int $startPosition, int $endPosition) : array
    {
        $tokens = $file->getTokens();

        $content = [];
        for ($i = $startPosition; $i < $endPosition; $i++) {
            $content[$i] = $tokens[$i]['content'];
        }

        return $content;
    }
}

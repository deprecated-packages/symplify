<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\SnifferAnalyzer;

use PHP_CodeSniffer\Files\File;

final class PositionFinder
{
    public static function findFirstPositionInCurrentLine(File $file, int $position): int
    {
        $currentPosition = $position;

        $line = $file->getTokens()[$position]['line'];
        while ($file->getTokens()[$currentPosition]['line'] === $line) {
            --$currentPosition;
        }

        return $currentPosition;
    }

    public static function findLastPositionInCurrentLine(File $file, int $position): int
    {
        $currentPosition = $position;

        $line = $file->getTokens()[$position]['line'];
        while ($file->getTokens()[$currentPosition]['line'] === $line) {
            ++$currentPosition;
        }

        return $currentPosition;
    }
}

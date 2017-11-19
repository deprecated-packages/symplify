<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Helper;

use PHP_CodeSniffer\Files\File;

final class TokenFinder
{
    public static function findNextLinePosition(File $file, int $position): int
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

    /**
     * @return int[]
     */
    public static function findAllOfType(File $file, int $type, int $start, int $end): array
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

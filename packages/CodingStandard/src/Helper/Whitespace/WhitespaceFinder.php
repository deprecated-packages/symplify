<?php declare(strict_types = 1);

namespace Symplify\CodingStandard\Helper\Whitespace;

use PHP_CodeSniffer_File;

final class WhitespaceFinder
{
    public static function findNextEmptyLinePosition(PHP_CodeSniffer_File $file, int $position) : int
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
}

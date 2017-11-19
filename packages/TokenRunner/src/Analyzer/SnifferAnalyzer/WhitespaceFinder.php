<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Helper\Whitespace;

use PHP_CodeSniffer\Files\File;

final class WhitespaceFinder
{
    public static function findNextEmptyLinePosition(File $file, int $position): int
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

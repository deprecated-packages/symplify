<?php declare(strict_types = 1);

namespace Symplify\CodingStandard\Helper\Whitespace;

use PHP_CodeSniffer\Files\File;

final class EmptyLinesResizer
{
    public static function resizeLines(
        File $file,
        int $position,
        int $currentLineCount,
        int $desiredLineCount
    ) : void {
        if ($currentLineCount > $desiredLineCount) {
            self::reduceBlankLines($file, $position, $currentLineCount, $desiredLineCount);
        } elseif ($currentLineCount < $desiredLineCount) {
            self::increaseBlankLines($file, $position, $currentLineCount, $desiredLineCount);
        }
    }

    private static function reduceBlankLines(File $file, int $position, int $from, int $to) : void
    {
        for ($i = $from; $i > $to; $i--) {
            $file->fixer->replaceToken($position, '');
            $position++;
        }
    }

    private static function increaseBlankLines(File $file, int $position, int $from, int $to) : void
    {
        for ($i = $from; $i < $to; $i++) {
            $file->fixer->addContentBefore($position, PHP_EOL);
        }
    }
}

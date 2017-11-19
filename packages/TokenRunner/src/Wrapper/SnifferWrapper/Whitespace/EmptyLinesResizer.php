<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Helper\Whitespace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;

final class EmptyLinesResizer
{
    /**
     * @var Fixer
     */
    private static $fixer;

    public static function resizeLines(
        File $file,
        int $position,
        int $currentLineCount,
        int $desiredLineCount
    ): void {
        self::$fixer = $file->fixer;

        if ($currentLineCount > $desiredLineCount) {
            self::reduceBlankLines($position, $currentLineCount, $desiredLineCount);
        } elseif ($currentLineCount < $desiredLineCount) {
            self::increaseBlankLines($position, $currentLineCount, $desiredLineCount);
        }
    }

    private static function reduceBlankLines(int $position, int $from, int $to): void
    {
        for ($i = $from; $i > $to; --$i) {
            self::$fixer->replaceToken($position, '');
            ++$position;
        }
    }

    private static function increaseBlankLines(int $position, int $from, int $to): void
    {
        for ($i = $from; $i < $to; ++$i) {
            self::$fixer->addContentBefore($position, PHP_EOL);
        }
    }
}

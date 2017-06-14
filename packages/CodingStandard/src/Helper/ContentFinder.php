<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Helper;

use PHP_CodeSniffer\Files\File;

final class ContentFinder
{
    public static function getContentBetween(File $file, int $startPosition, int $endPosition): string
    {
        $tokens = self::getTokensBetween($file, $startPosition, $endPosition);

        return implode('', $tokens);
    }

    /**
     * @return mixed[]
     */
    public static function getTokensBetween(File $file, int $startPosition, int $endPosition): array
    {
        $tokens = $file->getTokens();

        $content = [];
        for ($i = $startPosition; $i < $endPosition; ++$i) {
            $content[$i] = $tokens[$i]['content'];
        }

        return $content;
    }
}

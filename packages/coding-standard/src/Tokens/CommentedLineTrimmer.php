<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tokens;

/**
 * Heavily inspired by
 *
 * @see https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/PHP/CommentedOutCodeSniff.php
 */
final class CommentedLineTrimmer
{
    /**
     * @var string[]
     */
    private const OPENING_LINE = ['//', '#'];

    public function trim(string $tokenContent): string
    {
        foreach (self::OPENING_LINE as $openingLine) {
            if (! \str_starts_with($tokenContent, $openingLine)) {
                continue;
            }

            return substr($tokenContent, strlen($openingLine));
        }

        return $tokenContent;
    }
}

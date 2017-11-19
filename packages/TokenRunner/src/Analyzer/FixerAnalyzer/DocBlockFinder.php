<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class DocBlockFinder
{
    public static function findPreviousPosition(Tokens $tokens, int $index): ?int
    {
        for ($i = $index; $i > 0; --$i) {
            $token = $tokens[$i];

            if ($token->getContent() === ';') {
                return null;
            }

            if ($token->isComment()) {
                return $i;
            }
        }

        return null;
    }

    public static function findPrevious(Tokens $tokens, int $index): ?Token
    {
        $docBlockPosition = self::findPreviousPosition($tokens, $index);
        if ($docBlockPosition) {
            return $tokens[$docBlockPosition];
        }

        return null;
    }
}

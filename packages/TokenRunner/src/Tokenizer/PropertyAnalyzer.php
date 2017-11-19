<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tokenizer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class PropertyAnalyzer
{
    public static function findVisibilityPosition(Tokens $tokens, int $index): ?int
    {
        for ($i = 0; $i < 5; ++$i) {
            $possibleVisibilityTokenPosition = $tokens->getPrevNonWhitespace($index - $i);
            if ($possibleVisibilityTokenPosition === null) {
                break;
            }

            $possibleVisibilityToken = $tokens[$possibleVisibilityTokenPosition];
            if ($possibleVisibilityToken->isGivenKind([T_PUBLIC, T_PROTECTED, T_PRIVATE])) {
                return $possibleVisibilityTokenPosition;
            }
        }

        return null;
    }

    public static function findVisibility(Tokens $tokens, int $index): ?Token
    {
        $visibilityPosition = self::findVisibilityPosition($tokens, $index);
        if ($visibilityPosition === null) {
            return null;
        }

        return $tokens[$visibilityPosition];
    }
}

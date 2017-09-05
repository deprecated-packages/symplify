<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tokenizer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class DocBlockFinder
{
    public static function findPrevious(Tokens $tokens, int $index): ?Token
    {
        for ($i = 0; $i < 8; ++$i) {
            $possibleDocBlockTokenPosition = $tokens->getPrevNonWhitespace($index - $i);
            if ($possibleDocBlockTokenPosition === null) {
                break;
            }

            $possibleDocBlockToken = $tokens[$possibleDocBlockTokenPosition];
            if ($possibleDocBlockToken->isComment()) {
                return $possibleDocBlockToken;
            }
        }

        return null;
    }
}

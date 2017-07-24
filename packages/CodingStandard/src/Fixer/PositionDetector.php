<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class PositionDetector
{
    /**
     * @param Tokens|Token[] $tokens
     */
    public static function detectConstructorPosition(Tokens $tokens): ?int
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_FUNCTION)) {
                $namePosition = $tokens->getNextMeaningfulToken($index);
                $methodNameToken = $tokens[$namePosition];
                if ($methodNameToken->getContent() === '__construct') {
                    return $index;
                }
            }
        }

        return null;
    }
}

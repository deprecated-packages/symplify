<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;

final class TokenBuilder
{
    /**
     * Generates token for code like: " = []".
     *
     * @return Token[]
     */
    public static function createDefaultArrayTokens(): array
    {
        return [
            new Token([T_WHITESPACE, ' ']),
            new Token('='),
            new Token([T_WHITESPACE, ' ']),
            new Token([CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']),
            new Token([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']),
        ];
    }
}

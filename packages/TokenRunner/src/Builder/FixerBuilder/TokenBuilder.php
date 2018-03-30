<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Builder\FixerBuilder;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;

final class TokenBuilder
{
    /**
     * Generates: " = []".
     *
     * @return Token[]
     */
    public function createDefaultArrayTokens(): array
    {
        return [
            new Token([T_WHITESPACE, ' ']),
            new Token('='),
            new Token([T_WHITESPACE, ' ']),
            new Token([CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']),
            new Token([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']),
        ];
    }

    /**
     * Generates: "declare(strict_types=1);"
     *
     * @return Token[]
     */
    public function getDeclareStrictTypeSequence(): array
    {
        static $tokens = null;

        if ($tokens === null) {
            $tokens = [
                new Token([T_DECLARE, 'declare']),
                new Token('('),
                new Token([T_STRING, 'strict_types']),
                new Token('='),
                new Token([T_LNUMBER, '1']),
                new Token(')'),
                new Token(';'),
            ];
        }

        return $tokens;
    }
}

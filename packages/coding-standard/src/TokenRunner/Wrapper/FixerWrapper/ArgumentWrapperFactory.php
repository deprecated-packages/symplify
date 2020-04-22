<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Guard\TokenTypeGuard;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\ArgumentWrapper;

final class ArgumentWrapperFactory
{
    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    public function __construct(TokenTypeGuard $tokenTypeGuard)
    {
        $this->tokenTypeGuard = $tokenTypeGuard;
    }

    /**
     * @return ArgumentWrapper[]
     */
    public function createArgumentsFromTokensAndFunctionPosition(Tokens $tokens, int $position): array
    {
        $this->tokenTypeGuard->ensureIsTokenType($tokens[$position], [T_FUNCTION], __METHOD__);

        $argumentsBracketStart = $tokens->getNextTokenOfKind($position, ['(']);
        $argumentsBracketEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $argumentsBracketStart);

        // no arguments, return
        if ($argumentsBracketStart === $argumentsBracketEnd + 1) {
            return [];
        }

        $arguments = [];
        for ($i = $argumentsBracketStart + 1; $i < $argumentsBracketEnd; ++$i) {
            $token = $tokens[$i];

            if (! $token->isGivenKind(T_VARIABLE)) {
                continue;
            }

            $arguments[] = $this->createFromTokensAndPosition($tokens, $i);
        }

        return $arguments;
    }

    private function createFromTokensAndPosition(Tokens $tokens, int $position): ArgumentWrapper
    {
        return new ArgumentWrapper($tokens, $position);
    }
}

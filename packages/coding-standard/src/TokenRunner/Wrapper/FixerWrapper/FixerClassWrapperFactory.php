<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Guard\TokenTypeGuard;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\FixerClassWrapper;

final class FixerClassWrapperFactory
{
    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    public function __construct(TokenTypeGuard $tokenTypeGuard)
    {
        $this->tokenTypeGuard = $tokenTypeGuard;
    }

    public function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): FixerClassWrapper
    {
        /** @var Token $token */
        $token = $tokens[$startIndex];
        $this->tokenTypeGuard->ensureIsTokenType($token, [T_CLASS, T_INTERFACE, T_TRAIT], self::class);

        return new FixerClassWrapper($tokens, $startIndex);
    }
}

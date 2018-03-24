<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class MethodCallWrapperFactory
{
    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    public function __construct(TokenTypeGuard $tokenTypeGuard)
    {
        $this->tokenTypeGuard = $tokenTypeGuard;
    }

    public function createFromTokensAndPosition(Tokens $tokens, int $position): MethodCallWrapper
    {
        $this->tokenTypeGuard->ensureIsTokenType($tokens[$position], [T_STRING], __METHOD__);

        return new MethodCallWrapper($tokens, $position);
    }
}

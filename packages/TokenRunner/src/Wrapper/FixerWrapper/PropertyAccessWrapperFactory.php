<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class PropertyAccessWrapperFactory
{
    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    public function __construct(TokenTypeGuard $tokenTypeGuard)
    {
        $this->tokenTypeGuard = $tokenTypeGuard;
    }

    public function createFromTokensAndPosition(Tokens $tokens, int $position): PropertyAccessWrapper
    {
        $this->tokenTypeGuard->ensureIsTokenType($tokens[$position], [T_VARIABLE], __METHOD__);

        return new PropertyAccessWrapper($tokens, $position);
    }
}

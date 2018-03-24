<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ArgumentWrapperFactory
{
    public function createFromTokensAndPosition(Tokens $tokens, int $position): ArgumentWrapper
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$position], [T_VARIABLE], __METHOD__);

        return new ArgumentWrapper($tokens, $position);
    }
}

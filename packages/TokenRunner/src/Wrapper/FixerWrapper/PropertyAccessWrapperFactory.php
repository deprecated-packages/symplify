<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class PropertyAccessWrapperFactory
{
    public function createFromTokensAndPosition(Tokens $tokens, int $position): PropertyAccessWrapper
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$position], [T_VARIABLE], __METHOD__);

        return new PropertyAccessWrapper($tokens, $position);
    }
}

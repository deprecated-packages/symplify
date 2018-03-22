<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ArrayWrapperFactory
{
    public function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): ArrayWrapper
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$startIndex], [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN], __METHOD__);

        return new ArrayWrapper($tokens, $startIndex);
    }
}

<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ArrayWrapperFactory
{
    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    public function __construct(TokenSkipper $tokenSkipper, TokenTypeGuard $tokenTypeGuard)
    {
        $this->tokenSkipper = $tokenSkipper;
        $this->tokenTypeGuard = $tokenTypeGuard;
    }

    public function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): ArrayWrapper
    {
        $this->tokenTypeGuard->ensureIsTokenType($tokens[$startIndex], [
            T_ARRAY,
            CT::T_ARRAY_SQUARE_BRACE_OPEN
        ], __METHOD__);

        return new ArrayWrapper($tokens, $startIndex, $this->tokenSkipper);
    }
}

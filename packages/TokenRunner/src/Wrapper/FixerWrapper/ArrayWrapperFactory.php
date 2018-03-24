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

    public function __construct(TokenSkipper $tokenSkipper)
    {
        $this->tokenSkipper = $tokenSkipper;
    }

    public function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): ArrayWrapper
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$startIndex], [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN], __METHOD__);

        return new ArrayWrapper($tokens, $startIndex, $this->tokenSkipper);
    }
}

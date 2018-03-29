<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndInfo;
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

    public function createFromTokensAndBlockStartAndEndInfo(
        Tokens $tokens,
        BlockStartAndEndInfo $blockStartAndEndInfo
    ): ArrayWrapper {
        $this->tokenTypeGuard->ensureIsTokenType($tokens[$blockStartAndEndInfo->getStart()], [
            T_ARRAY,
            CT::T_ARRAY_SQUARE_BRACE_OPEN,
        ], __METHOD__);

        return new ArrayWrapper(
            $tokens,
            $blockStartAndEndInfo->getStart(),
            $blockStartAndEndInfo->getEnd(),
            $this->tokenSkipper
        );
    }
}

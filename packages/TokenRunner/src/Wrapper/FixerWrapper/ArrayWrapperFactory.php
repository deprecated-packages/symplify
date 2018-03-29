<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;

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

    public function createFromTokensAndBlockStartAndEndInfo(
        Tokens $tokens,
        BlockStartAndEndInfo $blockStartAndEndInfo
    ): ArrayWrapper {
        return new ArrayWrapper(
            $tokens,
            $blockStartAndEndInfo->getStart(),
            $blockStartAndEndInfo->getEnd(),
            $this->tokenSkipper
        );
    }
}

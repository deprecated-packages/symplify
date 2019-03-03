<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockInfo;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;

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

    public function createFromTokensAndBlockInfo(Tokens $tokens, BlockInfo $blockInfo): ArrayWrapper
    {
        return new ArrayWrapper($tokens, $blockInfo->getStart(), $blockInfo->getEnd(), $this->tokenSkipper);
    }
}

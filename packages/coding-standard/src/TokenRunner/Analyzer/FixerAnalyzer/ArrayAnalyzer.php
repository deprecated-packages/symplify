<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

final class ArrayAnalyzer
{
    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    public function __construct(TokenSkipper $tokenSkipper)
    {
        $this->tokenSkipper = $tokenSkipper;
    }

    public function getItemCount(Tokens $tokens, BlockInfo $blockInfo): int
    {
        $nextMeanninfulPosition = $tokens->getNextMeaningfulToken($blockInfo->getStart());
        if ($nextMeanninfulPosition === null) {
            return 0;
        }

        /** @var Token $nextMeaningfulToken */
        $nextMeaningfulToken = $tokens[$nextMeanninfulPosition];

        // no elements
        if ($this->isArrayCloser($nextMeaningfulToken)) {
            return 0;
        }

        $itemCount = 1;
        for ($i = $blockInfo->getEnd() - 1; $i >= $blockInfo->getStart() + 1; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($tokens, $i);

            /** @var Token $token */
            $token = $tokens[$i];
            if ($token->getContent() === ',') {
                ++$itemCount;
            }
        }

        return $itemCount;
    }

    public function isIndexedList(Tokens $tokens, BlockInfo $blockInfo): bool
    {
        for ($i = $blockInfo->getEnd() - 1; $i >= $blockInfo->getStart() + 1; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($tokens, $i);

            /** @var Token $token */
            $token = $tokens[$i];
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                return true;
            }
        }

        return false;
    }

    private function isArrayCloser(Token $nextMeaningfulToken): bool
    {
        if ($nextMeaningfulToken->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            return true;
        }

        return $nextMeaningfulToken->getContent() === ')';
    }
}

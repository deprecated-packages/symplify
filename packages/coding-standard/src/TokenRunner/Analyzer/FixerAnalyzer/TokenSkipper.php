<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

final class TokenSkipper
{
    /**
     * @var BlockFinder
     */
    private $blockFinder;

    public function __construct(BlockFinder $blockFinder)
    {
        $this->blockFinder = $blockFinder;
    }

    public function skipBlocks(Tokens $tokens, int $position): int
    {
        $token = $tokens[$position];

        if ($token->getContent() === '{') {
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
            if ($blockInfo === null) {
                return $position;
            }

            return $blockInfo->getEnd();
        }

        if ($token->isGivenKind([CT::T_ARRAY_SQUARE_BRACE_OPEN, T_ARRAY])) {
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
            if ($blockInfo === null) {
                return $position;
            }

            return $blockInfo->getEnd();
        }

        return $position;
    }

    public function skipBlocksReversed(Tokens $tokens, int $position): int
    {
        $token = $tokens[$position];
        if (! $token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE) && ! $token->equals(')')) {
            return $position;
        }

        return $this->blockFinder->findInTokensByEdge($tokens, $position)->getStart();
    }
}

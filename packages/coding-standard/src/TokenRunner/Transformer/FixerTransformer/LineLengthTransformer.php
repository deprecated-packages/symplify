<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

final class LineLengthTransformer
{
    public function __construct(
        private LineLengthResolver $lineLengthResolver,
        private TokensInliner $tokensInliner,
        private FirstLineLengthResolver $firstLineLengthResolver,
        private TokensNewliner $tokensNewliner
    ) {
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function fixStartPositionToEndPosition(
        BlockInfo $blockInfo,
        Tokens $tokens,
        int $lineLength,
        bool $breakLongLines,
        bool $inlineShortLine
    ): void {
        if ($this->hasPromotedProperty($tokens, $blockInfo)) {
            return;
        }

        $firstLineLength = $this->firstLineLengthResolver->resolveFromTokensAndStartPosition($tokens, $blockInfo);
        if ($firstLineLength > $lineLength && $breakLongLines) {
            $this->tokensNewliner->breakItems($blockInfo, $tokens);
            return;
        }

        $fullLineLength = $this->lineLengthResolver->getLengthFromStartEnd($tokens, $blockInfo);
        if ($fullLineLength <= $lineLength && $inlineShortLine) {
            $this->tokensInliner->inlineItems($tokens, $blockInfo);
            return;
        }
    }

    /**
     * @param Tokens<Token> $tokens
     */
    private function hasPromotedProperty(Tokens $tokens, BlockInfo $blockInfo): bool
    {
        $resultByKind = $tokens->findGivenKind([
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
        ], $blockInfo->getStart(), $blockInfo->getEnd());

        return (bool) array_filter($resultByKind);
    }
}

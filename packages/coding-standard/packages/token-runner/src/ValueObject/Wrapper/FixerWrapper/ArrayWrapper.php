<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;

final class ArrayWrapper
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var int
     */
    private $endIndex;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    public function __construct(Tokens $tokens, int $startIndex, int $endIndex, TokenSkipper $tokenSkipper)
    {
        $this->tokens = $tokens;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
        $this->tokenSkipper = $tokenSkipper;
    }

    public function isAssociativeArray(): bool
    {
        for ($i = $this->startIndex + 1; $i <= $this->endIndex - 1; ++$i) {
            $i = $this->tokenSkipper->skipBlocks($this->tokens, $i);

            $token = $this->tokens[$i];

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                return true;
            }
        }

        return false;
    }

    public function getItemCount(): int
    {
        $itemCount = 0;
        for ($i = $this->endIndex - 1; $i >= $this->startIndex; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($this->tokens, $i);

            $token = $this->tokens[$i];
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                ++$itemCount;
            }
        }

        return $itemCount;
    }

    public function isFirstItemArray(): bool
    {
        for ($i = $this->endIndex - 1; $i >= $this->startIndex; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($this->tokens, $i);

            $token = $this->tokens[$i];
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $nextTokenAfterArrowPosition = $this->tokens->getNextNonWhitespace($i);
                if ($nextTokenAfterArrowPosition === null) {
                    return false;
                }

                $nextToken = $this->tokens[$nextTokenAfterArrowPosition];

                return $nextToken->isGivenKind(self::ARRAY_OPEN_TOKENS);
            }
        }

        return false;
    }
}

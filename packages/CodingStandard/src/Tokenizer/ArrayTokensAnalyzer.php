<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tokenizer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class ArrayTokensAnalyzer
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var Token
     */
    private $startToken;

    /**
     * @var int
     */
    private $endIndex;
    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    public function __construct(Tokens $tokens, int $startIndex)
    {
        $this->tokens = $tokens;
        $this->startIndex = $startIndex;
        $this->startToken = $tokens[$startIndex];
        $this->tokenSkipper = new TokenSkipper;
    }

    public function getStartIndex(): int
    {
        return $this->startIndex;
    }

    public function getEndIndex(): int
    {
        if ($this->endIndex) {
            return $this->endIndex;
        }

        if ($this->isOldArray()) {
            $this->endIndex = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $this->startIndex + 1);
        } else {
            $this->endIndex = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $this->startIndex);
        }

        return $this->endIndex;
    }

    public function isOldArray(): bool
    {
        return (bool) $this->startToken->isGivenKind(T_ARRAY);
    }

    public function isAssociativeArray(): bool
    {
        for ($i = $this->startIndex + 1; $i <= $this->getEndIndex() - 1; ++$i) {
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
        for ($i = $this->getEndIndex(); $i >= $this->startIndex; --$i) {
            $token = $this->tokens[$i];
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                ++$itemCount;
            }
        }

        return $itemCount;
    }
}

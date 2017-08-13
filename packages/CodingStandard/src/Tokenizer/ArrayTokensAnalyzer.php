<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tokenizer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

final class ArrayTokensAnalyzer
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var int[]
     */
    private const ARRAY_CLOSING_TOKENS = [')', CT::T_ARRAY_SQUARE_BRACE_CLOSE];

    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var int
     */
    private $startIndex;

    public function __construct(Tokens $tokens, int $startIndex)
    {
        $this->tokens = $tokens;
        $this->startIndex = $startIndex;
        $this->startToken = $tokens[$startIndex];
    }

    public function isOldArray(): bool
    {
        return (bool) $this->startToken->isGivenKind(T_ARRAY);
    }

    public function getEndIndex(): int
    {
        if ($this->isOldArray()) {
            return $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $this->startIndex + 1);
        }

        return $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $this->startIndex);
    }

    public function isAssociativeArray(): bool
    {
        $isDivedInAnotherArray = false;

        for ($i = $this->startIndex + 1; $i <= $this->getEndIndex() - 1; ++$i) {
            $token = $this->tokens[$i];

            if ($isDivedInAnotherArray === false && $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                $isDivedInAnotherArray = true;
            } elseif ($isDivedInAnotherArray && $token->isGivenKind(self::ARRAY_CLOSING_TOKENS)) {
                $isDivedInAnotherArray = false;
            }

            // do not process dived arrays in this run
            if ($isDivedInAnotherArray) {
                continue;
            }

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

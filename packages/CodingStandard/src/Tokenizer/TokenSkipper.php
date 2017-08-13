<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tokenizer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

final class TokenSkipper
{
    public function skipBlocks(Tokens $tokens, int $i): int
    {
        $tokenCountToSkip = 0;
        $token = $tokens[$i];

        if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
            $blockEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $i);
            $tokenCountToSkip = $blockEnd - $i;
        }

        if ($token->isGivenKind(T_ARRAY) && $token->getContent() === '(') {
            $blockEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i);
            $tokenCountToSkip = $blockEnd - $i;
        }

        return $i + $tokenCountToSkip;
    }

    public function skipBlocksReversed(Tokens $tokens, int $i): int
    {
        $tokenCountToSkip = 0;
        $token = $tokens[$i];

        if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            $blockStart = $this->findBlockStart($tokens, Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $i);
            $tokenCountToSkip = $i - $blockStart;
        }

        if ($token->getContent() === ')') {
            $blockStart = $this->findBlockStart($tokens, Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i);
            $tokenCountToSkip = $i - $blockStart;
        }

        return $i - $tokenCountToSkip;
    }

    private function findBlockStart(Tokens $tokens, int $type, int $i): int
    {
        return $tokens->findBlockEnd($type, $i, false);
    }
}

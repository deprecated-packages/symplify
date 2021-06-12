<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer;
use Symplify\CodingStandard\TokenRunner\TokenFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineKind;

final class LineLengthCloserTransformer
{
    public function __construct(
        private CallAnalyzer $callAnalyzer,
        private TokenFinder $tokenFinder
    ) {
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function insertNewlineBeforeClosingIfNeeded(
        Tokens $tokens,
        BlockInfo $blockInfo,
        int $kind,
        string $newlineIndentWhitespace,
        string $closingBracketNewlineIndentWhitespace
    ): void {
        $isMethodCall = $this->callAnalyzer->isMethodCall($tokens, $blockInfo->getStart());
        $endIndex = $blockInfo->getEnd();

        $previousToken = $this->tokenFinder->getPreviousMeaningfulToken($tokens, $endIndex);
        $previousPreviousToken = $this->tokenFinder->getPreviousMeaningfulToken($tokens, $previousToken);

        // special case, if the function is followed by array - method([...]) - but not - method([[...]]))
        if ($this->shouldAddNewlineEarlier($previousToken, $previousPreviousToken, $isMethodCall, $kind)) {
            $tokens->ensureWhitespaceAtIndex($endIndex - 1, 0, $newlineIndentWhitespace);
            return;
        }

        $tokens->ensureWhitespaceAtIndex($endIndex - 1, 1, $closingBracketNewlineIndentWhitespace);
    }

    private function shouldAddNewlineEarlier(
        Token $previousToken,
        Token $previousPreviousToken,
        bool $isMethodCall,
        int $kind
    ): bool {
        if ($isMethodCall) {
            return false;
        }

        if ($kind !== LineKind::CALLS) {
            return false;
        }

        if (! $previousToken->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            return false;
        }

        return ! $previousPreviousToken->isGivenKind([CT::T_ARRAY_SQUARE_BRACE_CLOSE, CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }
}

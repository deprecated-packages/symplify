<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\TokenFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

final class LineLengthTransformer
{
    /**
     * @var string
     */
    private $indentWhitespace;

    /**
     * @var string
     */
    private $newlineIndentWhitespace;

    /**
     * @var string
     */
    private $closingBracketNewlineIndentWhitespace;

    /**
     * @var IndentDetector
     */
    private $indentDetector;

    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var LineLengthResolver
     */
    private $lineLengthResolver;

    /**
     * @var TokensInliner
     */
    private $tokensInliner;

    /**
     * @var FirstLineLengthResolver
     */
    private $firstLineLengthResolver;

    /**
     * @var TokenFinder
     */
    private $tokenFinder;

    /**
     * @var CallAnalyzer
     */
    private $callAnalyzer;

    public function __construct(
        IndentDetector $indentDetector,
        TokenSkipper $tokenSkipper,
        WhitespacesFixerConfig $whitespacesFixerConfig,
        LineLengthResolver $lineLengthResolver,
        TokensInliner $tokensInliner,
        FirstLineLengthResolver $firstLineLengthResolver,
        TokenFinder $tokenFinder,
        CallAnalyzer $callAnalyzer
    ) {
        $this->indentDetector = $indentDetector;
        $this->tokenSkipper = $tokenSkipper;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->lineLengthResolver = $lineLengthResolver;
        $this->tokensInliner = $tokensInliner;
        $this->firstLineLengthResolver = $firstLineLengthResolver;
        $this->tokenFinder = $tokenFinder;
        $this->callAnalyzer = $callAnalyzer;
    }

    public function fixStartPositionToEndPosition(
        BlockInfo $blockInfo,
        Tokens $tokens,
        int $lineLength,
        bool $breakLongLines,
        bool $inlineShortLine
    ): void {
        $firstLineLength = $this->firstLineLengthResolver->resolveFromTokensAndStartPosition($tokens, $blockInfo);
        if ($firstLineLength > $lineLength && $breakLongLines) {
            $this->breakItems($blockInfo, $tokens);
            return;
        }

        $fullLineLength = $this->lineLengthResolver->getLengthFromStartEnd($tokens, $blockInfo);
        if ($fullLineLength <= $lineLength && $inlineShortLine) {
            $this->tokensInliner->inlineItems($tokens, $blockInfo);
            return;
        }
    }

    /**
     * @param Tokens|Token[] $tokens
     */
    public function breakItems(BlockInfo $blockInfo, Tokens $tokens): void
    {
        $this->prepareIndentWhitespaces($tokens, $blockInfo->getStart());

        // from bottom top, to prevent skipping ids
        //  e.g when token is added in the middle, the end index does now point to earlier element!

        // 1. break before arguments closing
        $this->insertNewlineBeforeClosingIfNeeded($tokens, $blockInfo);

        // again, from the bottom to the top
        for ($i = $blockInfo->getEnd() - 1; $i > $blockInfo->getStart(); --$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];

            $i = $this->tokenSkipper->skipBlocksReversed($tokens, $i);

            // 2. new line after each comma ",", instead of just space
            if ($currentToken->getContent() === ',') {
                if ($this->isLastItem($tokens, $i)) {
                    continue;
                }

                if ($this->isFollowedByComment($tokens, $i)) {
                    continue;
                }

                $tokens->ensureWhitespaceAtIndex($i + 1, 0, $this->newlineIndentWhitespace);
            }
        }

        // 3. break after arguments opening
        $this->insertNewlineAfterOpeningIfNeeded($tokens, $blockInfo->getStart());
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $startIndex): void
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $startIndex);

        $this->indentWhitespace = str_repeat($this->whitespacesFixerConfig->getIndent(), $indentLevel + 1);
        $this->closingBracketNewlineIndentWhitespace = $this->whitespacesFixerConfig->getLineEnding() . str_repeat(
            $this->whitespacesFixerConfig->getIndent(),
            $indentLevel
        );

        $this->newlineIndentWhitespace = $this->whitespacesFixerConfig->getLineEnding() . $this->indentWhitespace;
    }

    private function insertNewlineBeforeClosingIfNeeded(Tokens $tokens, BlockInfo $blockInfo): void
    {
        $isMethodCall = $this->callAnalyzer->isMethodCall($tokens, $blockInfo->getStart());
        $endIndex = $blockInfo->getEnd();

        $previousToken = $this->tokenFinder->getPreviousMeaningfulToken($tokens, $endIndex);
        $previousPreviousToken = $this->tokenFinder->getPreviousMeaningfulToken($tokens, $previousToken);

        // special case, if the function is followed by array - method([...]) - but not - method([[...]]))
        if ($previousToken->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE) && ! $previousPreviousToken->isGivenKind(
            [CT::T_ARRAY_SQUARE_BRACE_CLOSE, CT::T_ARRAY_SQUARE_BRACE_OPEN]
        ) && ! $isMethodCall) {
            $tokens->ensureWhitespaceAtIndex($endIndex - 1, 0, $this->newlineIndentWhitespace);
            return;
        }

        $tokens->ensureWhitespaceAtIndex($endIndex - 1, 1, $this->closingBracketNewlineIndentWhitespace);
    }

    /**
     * Has already newline? usually the last line => skip to prevent double spacing
     * @param Tokens|Token[] $tokens
     */
    private function isLastItem(Tokens $tokens, int $position): bool
    {
        $nextPosition = $position + 1;
        if (! isset($tokens[$nextPosition])) {
            throw new TokenNotFoundException($nextPosition);
        }

        $tokenContent = $tokens[$nextPosition]->getContent();
        return Strings::contains($tokenContent, $this->whitespacesFixerConfig->getLineEnding());
    }

    /**
     * @param Tokens|Token[] $tokens
     */
    private function isFollowedByComment(Tokens $tokens, int $i): bool
    {
        $nextToken = $tokens[$i + 1];
        $nextNextToken = $tokens[$i + 2];

        if ($nextNextToken->isComment()) {
            return true;
        }

        // if next token is just space, turn it to newline
        return $nextToken->isWhitespace(' ') && $nextNextToken->isComment();
    }

    private function insertNewlineAfterOpeningIfNeeded(Tokens $tokens, int $blockStartIndex): void
    {
        if (! isset($tokens[$blockStartIndex + 1])) {
            throw new TokenNotFoundException($blockStartIndex + 1);
        }

        /** @var Token $nextToken */
        $nextToken = $tokens[$blockStartIndex + 1];

        if ($nextToken->isGivenKind(T_WHITESPACE)) {
            $tokens->ensureWhitespaceAtIndex($blockStartIndex + 1, 0, $this->newlineIndentWhitespace);
            return;
        }

        // special case, if the function is followed by array - method([...])
        if ($nextToken->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN]) && ! $this->callAnalyzer->isMethodCall(
            $tokens,
            $blockStartIndex
        )) {
            $tokens->ensureWhitespaceAtIndex($blockStartIndex + 1, 1, $this->newlineIndentWhitespace);
            return;
        }

        $tokens->ensureWhitespaceAtIndex($blockStartIndex, 1, $this->newlineIndentWhitespace);
    }
}

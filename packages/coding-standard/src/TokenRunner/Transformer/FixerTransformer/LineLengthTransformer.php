<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineKind;

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
     * @var LineLengthCloserTransformer
     */
    private $lineLengthCloserTransformer;

    /**
     * @var LineLengthOpenerTransformer
     */
    private $lineLengthOpenerTransformer;

    public function __construct(
        IndentDetector $indentDetector,
        TokenSkipper $tokenSkipper,
        WhitespacesFixerConfig $whitespacesFixerConfig,
        LineLengthResolver $lineLengthResolver,
        TokensInliner $tokensInliner,
        FirstLineLengthResolver $firstLineLengthResolver,
        LineLengthCloserTransformer $lineLengthCloserTransformer,
        LineLengthOpenerTransformer $lineLengthOpenerTransformer
    ) {
        $this->indentDetector = $indentDetector;
        $this->tokenSkipper = $tokenSkipper;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->lineLengthResolver = $lineLengthResolver;
        $this->tokensInliner = $tokensInliner;
        $this->firstLineLengthResolver = $firstLineLengthResolver;
        $this->lineLengthCloserTransformer = $lineLengthCloserTransformer;
        $this->lineLengthOpenerTransformer = $lineLengthOpenerTransformer;
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
    public function breakItems(BlockInfo $blockInfo, Tokens $tokens, int $kind = LineKind::CALLS): void
    {
        $this->prepareIndentWhitespaces($tokens, $blockInfo->getStart());

        // from bottom top, to prevent skipping ids
        //  e.g when token is added in the middle, the end index does now point to earlier element!

        // 1. break before arguments closing
        $this->lineLengthCloserTransformer->insertNewlineBeforeClosingIfNeeded(
            $tokens,
            $blockInfo,
            $kind,
            $this->newlineIndentWhitespace,
            $this->closingBracketNewlineIndentWhitespace
        );

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
        $this->lineLengthOpenerTransformer->insertNewlineAfterOpeningIfNeeded(
            $tokens,
            $blockInfo->getStart(),
            $this->newlineIndentWhitespace
        );
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
}

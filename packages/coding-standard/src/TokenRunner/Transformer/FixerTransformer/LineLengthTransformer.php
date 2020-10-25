<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObjectFactory\LineLengthAndPositionFactory;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;

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
     * @var LineLengthAndPositionFactory
     */
    private $lineLengthAndPositionFactory;

    /**
     * @var TokensInliner
     */
    private $tokensInliner;

    public function __construct(
        IndentDetector $indentDetector,
        TokenSkipper $tokenSkipper,
        WhitespacesFixerConfig $whitespacesFixerConfig,
        LineLengthResolver $lineLengthResolver,
        LineLengthAndPositionFactory $lineLengthAndPositionFactory,
        TokensInliner $tokensInliner
    ) {
        $this->indentDetector = $indentDetector;
        $this->tokenSkipper = $tokenSkipper;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->lineLengthResolver = $lineLengthResolver;
        $this->lineLengthAndPositionFactory = $lineLengthAndPositionFactory;
        $this->tokensInliner = $tokensInliner;
    }

    public function fixStartPositionToEndPosition(
        BlockInfo $blockInfo,
        Tokens $tokens,
        int $lineLength,
        bool $breakLongLines,
        bool $inlineShortLine
    ): void {
        $firstLineLength = $this->getFirstLineLength($blockInfo->getStart(), $tokens);
        if ($firstLineLength > $lineLength && $breakLongLines) {
            $this->breakItems($blockInfo, $tokens);
            return;
        }

        $fullLineLength = $this->lineLengthResolver->getLengthFromStartEnd($blockInfo, $tokens);
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
        $this->insertNewlineBeforeClosingIfNeeded($tokens, $blockInfo->getEnd());

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

    /**
     * @param Tokens|Token[] $tokens
     */
    private function getFirstLineLength(int $startPosition, Tokens $tokens): int
    {
        // compute from here to start of line
        $currentPosition = $startPosition;

        // collect length of tokens on current line which precede token at $currentPosition
        $lineLengthAndPosition = $this->lineLengthAndPositionFactory->createFromTokensAndLineStartPosition(
            $tokens,
            $currentPosition
        );
        $lineLength = $lineLengthAndPosition->getLineLength();
        $currentPosition = $lineLengthAndPosition->getCurrentPosition();

        /** @var Token $currentToken */
        $currentToken = $tokens[$currentPosition];

        // includes indent in the beginning
        $lineLength += strlen($currentToken->getContent());

        // minus end of lines, do not count line feeds as characters
        $endOfLineCount = substr_count($currentToken->getContent(), StaticEolConfiguration::getEolChar());
        $lineLength -= $endOfLineCount;

        // compute from here to end of line
        $currentPosition = $startPosition + 1;

        // collect length of tokens on current line which follow token at $currentPosition
        while (! $this->isEndOFArgumentsLine($tokens, $currentPosition)) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$currentPosition];

            // in case of multiline string, we are interested in length of the part on current line only
            $explode = explode("\n", $currentToken->getContent(), 2);
            // string follows current token, so we are interested in beginning only
            $lineLength += strlen($explode[0]);

            ++$currentPosition;

            if (count($explode) > 1) {
                // no longer need to continue searching for end of arguments
                break;
            }

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return $lineLength;
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

    private function insertNewlineBeforeClosingIfNeeded(Tokens $tokens, int $arrayEndIndex): void
    {
        $tokens->ensureWhitespaceAtIndex($arrayEndIndex - 1, 1, $this->closingBracketNewlineIndentWhitespace);
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

        if ($tokens[$blockStartIndex + 1]->isGivenKind(T_WHITESPACE)) {
            $tokens->ensureWhitespaceAtIndex($blockStartIndex + 1, 0, $this->newlineIndentWhitespace);
            return;
        }

        $tokens->ensureWhitespaceAtIndex($blockStartIndex, 1, $this->newlineIndentWhitespace);
    }

    /**
     * @param Tokens|Token[] $tokens
     */
    private function isEndOFArgumentsLine(Tokens $tokens, int $position): bool
    {
        if (! isset($tokens[$position])) {
            throw new TokenNotFoundException($position);
        }

        if (Strings::startsWith($tokens[$position]->getContent(), StaticEolConfiguration::getEolChar())) {
            return true;
        }

        return $tokens[$position]->isGivenKind(CT::T_USE_LAMBDA);
    }
}

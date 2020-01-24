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
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\PackageBuilder\Configuration\EolConfiguration;

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

    public function __construct(
        IndentDetector $indentDetector,
        TokenSkipper $tokenSkipper,
        WhitespacesFixerConfig $whitespacesFixerConfig
    ) {
        $this->indentDetector = $indentDetector;
        $this->tokenSkipper = $tokenSkipper;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
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

        $fullLineLength = $this->getLengthFromStartEnd($blockInfo, $tokens);
        if ($fullLineLength <= $lineLength && $inlineShortLine) {
            $this->inlineItems($blockInfo, $tokens);
            return;
        }
    }

    public function breakItems(BlockInfo $blockInfo, Tokens $tokens): void
    {
        $this->prepareIndentWhitespaces($tokens, $blockInfo->getStart());

        // from bottom top, to prevent skipping ids
        //  e.g when token is added in the middle, the end index does now point to earlier element!

        // 1. break before arguments closing
        $this->insertNewlineBeforeClosingIfNeeded($tokens, $blockInfo->getEnd());

        // again, from bottom top
        for ($i = $blockInfo->getEnd() - 1; $i > $blockInfo->getStart(); --$i) {
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

    private function getFirstLineLength(int $startPosition, Tokens $tokens): int
    {
        $lineLength = 0;

        // compute from here to start of line
        $currentPosition = $startPosition;

        // collect length of tokens on current line which precede token at $currentPosition
        [$lineLength, $currentPosition] = $this->getLengthToStartOfLine($tokens, $currentPosition);

        $currentToken = $tokens[$currentPosition];

        // includes indent in the beginning
        $lineLength += strlen($currentToken->getContent());

        // minus end of lines, do not count line feeds as characters
        $endOfLineCount = substr_count($currentToken->getContent(), EolConfiguration::getEolChar());
        $lineLength -= $endOfLineCount;

        // compute from here to end of line
        $currentPosition = $startPosition + 1;

        // collect length of tokens on current line which follow token at $currentPosition
        while (! $this->isEndOFArgumentsLine($tokens, $currentPosition)) {
            // in case of multiline string, we are interested in length of the part on current line only
            $explode = explode("\n", $tokens[$currentPosition]->getContent(), 2);
            // string follows current token, so we are interested in beginning only
            $lineLength += strlen($explode[0]);

            ++$currentPosition;

            if (count($explode) > 1) {
                break; // no longer need to continue searching for end of arguments
            }

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return $lineLength;
    }

    private function getLengthFromStartEnd(BlockInfo $blockInfo, Tokens $tokens): int
    {
        $lineLength = 0;

        // compute from function to start of line
        $currentPosition = $blockInfo->getStart();
        while (! $this->isNewLineOrOpenTag($tokens, $currentPosition)) {
            $lineLength += strlen($tokens[$currentPosition]->getContent());
            --$currentPosition;

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        // get spaces to first line
        $lineLength += strlen($tokens[$currentPosition]->getContent());

        // get length from start of function till end of arguments - with spaces as one
        $lineLength += $this->getLenthFromFunctionStartToEndOfArguments($blockInfo, $tokens);

        // get length from end or arguments to first line break
        $lineLength += $this->getLengthFromEndOfArgumentToLineBreak($blockInfo, $tokens);

        return $lineLength;
    }

    private function inlineItems(BlockInfo $blockInfo, Tokens $tokens): void
    {
        // replace line feeds with " "
        for ($i = $blockInfo->getStart() + 1; $i < $blockInfo->getEnd(); ++$i) {
            $currentToken = $tokens[$i];
            $i = $this->tokenSkipper->skipBlocks($tokens, $i);
            if (! $currentToken->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $previousToken = $tokens[$i - 1];
            $nextToken = $tokens[$i + 1];

            // do not clear before *doc end, removing spaces breaks stuff
            if ($previousToken->isGivenKind([T_START_HEREDOC, T_END_HEREDOC])) {
                continue;
            }

            // clear space after opening and before closing bracket
            if ($this->isBlockStartOrEnd($previousToken, $nextToken)) {
                $tokens->clearAt($i);
                continue;
            }

            $tokens[$i] = new Token([T_WHITESPACE, ' ']);
        }
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $arrayStartIndex);

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
     */
    private function isLastItem(Tokens $tokens, int $i): bool
    {
        return Strings::contains($tokens[$i + 1]->getContent(), $this->whitespacesFixerConfig->getLineEnding());
    }

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

    private function insertNewlineAfterOpeningIfNeeded(Tokens $tokens, int $arrayStartIndex): void
    {
        if ($tokens[$arrayStartIndex + 1]->isGivenKind(T_WHITESPACE)) {
            $tokens->ensureWhitespaceAtIndex($arrayStartIndex + 1, 0, $this->newlineIndentWhitespace);
            return;
        }

        $tokens->ensureWhitespaceAtIndex($arrayStartIndex, 1, $this->newlineIndentWhitespace);
    }

    /**
     * @return int[]
     */
    private function getLengthToStartOfLine(Tokens $tokens, int $currentPosition): array
    {
        $length = 0;

        while (! $this->isNewLineOrOpenTag($tokens, $currentPosition)) {
            // in case of multiline string, we are interested in length of the part on current line only
            $explode = explode("\n", $tokens[$currentPosition]->getContent());
            // string precedes current token, so we are interested in end part only
            if (count($explode) !== 0) {
                $lastSection = end($explode);
                $length += strlen($lastSection);
            }

            --$currentPosition;

            if (count($explode) > 1) {
                break; // no longer need to continue searching for newline
            }

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return [$length, $currentPosition];
    }

    private function isEndOFArgumentsLine(Tokens $tokens, int $position): bool
    {
        if (Strings::startsWith($tokens[$position]->getContent(), EolConfiguration::getEolChar())) {
            return true;
        }

        return $tokens[$position]->isGivenKind(CT::T_USE_LAMBDA);
    }

    private function isNewLineOrOpenTag(Tokens $tokens, int $position): bool
    {
        if (Strings::startsWith($tokens[$position]->getContent(), EolConfiguration::getEolChar())) {
            return true;
        }

        return $tokens[$position]->isGivenKind(T_OPEN_TAG);
    }

    private function getLenthFromFunctionStartToEndOfArguments(BlockInfo $blockInfo, Tokens $tokens): int
    {
        $length = 0;

        $currentPosition = $blockInfo->getStart();
        while ($currentPosition < $blockInfo->getEnd()) {
            $currentToken = $tokens[$currentPosition];
            if ($currentToken->isGivenKind(T_WHITESPACE)) {
                ++$length;
                ++$currentPosition;
                continue;
            }

            $length += strlen($tokens[$currentPosition]->getContent());
            ++$currentPosition;

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return $length;
    }

    private function getLengthFromEndOfArgumentToLineBreak(BlockInfo $blockInfo, Tokens $tokens): int
    {
        $length = 0;

        $currentPosition = $blockInfo->getEnd();
        while (! Strings::startsWith($tokens[$currentPosition]->getContent(), EolConfiguration::getEolChar())) {
            $currentToken = $tokens[$currentPosition];

            $length += strlen($currentToken->getContent());
            ++$currentPosition;

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return $length;
    }

    private function isBlockStartOrEnd(Token $previousToken, Token $nextToken): bool
    {
        if (in_array($previousToken->getContent(), ['(', '['], true)) {
            return true;
        }

        return in_array($nextToken->getContent(), [')', ']'], true);
    }
}

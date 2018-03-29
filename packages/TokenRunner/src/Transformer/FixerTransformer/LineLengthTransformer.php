<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Transformer\FixerTransformer;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\BlockStartAndEndInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\TokenRunner\Configuration\Configuration;

final class LineLengthTransformer
{
    /**
     * @var IndentDetector
     */
    private $indentDetector;

    /**
     * @var Configuration
     */
    private $configuration;

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
     * @var TokenSkipper
     */
    private $tokenSkipper;

    public function __construct(
        Configuration $configuration,
        IndentDetector $indentDetector,
        TokenSkipper $tokenSkipper
    ) {
        $this->indentDetector = $indentDetector;
        $this->configuration = $configuration;
        $this->tokenSkipper = $tokenSkipper;
    }

    public function fixStartPositionToEndPosition(
        BlockStartAndEndInfo $blockStartAndEndInfo,
        Tokens $tokens,
        int $currentPosition
    ): void {
        $this->prepareIndentWhitespaces($tokens, $blockStartAndEndInfo->getStart());

        $firstLineLength = $this->getFirstLineLength($blockStartAndEndInfo->getStart(), $tokens);
        if ($firstLineLength > $this->configuration->getMaxLineLength()) {
            $this->breakItems($blockStartAndEndInfo, $tokens);
            return;
        }

        $fullLineLength = $this->getLengthFromStartEnd($blockStartAndEndInfo, $tokens);

        if ($fullLineLength <= $this->configuration->getMaxLineLength()) {
            $this->inlineItems($blockStartAndEndInfo->getEnd(), $tokens, $currentPosition);
            return;
        }
    }

    public function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $arrayStartIndex, $this->configuration);

        $this->indentWhitespace = str_repeat($this->configuration->getIndent(), $indentLevel + 1);
        $this->closingBracketNewlineIndentWhitespace = $this->configuration->getLineEnding() . str_repeat(
            $this->configuration->getIndent(),
            $indentLevel
        );
        $this->newlineIndentWhitespace = $this->configuration->getLineEnding() . $this->indentWhitespace;
    }

    public function getFirstLineLength(int $startPosition, Tokens $tokens): int
    {
        $lineLength = 0;

        // compute from here to start of line
        $currentPosition = $startPosition;
        while (! Strings::startsWith($tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $lineLength += strlen($tokens[$currentPosition]->getContent());
            --$currentPosition;
        }

        $currentToken = $tokens[$currentPosition];

        // includes indent in the beginning
        $lineLength += strlen($currentToken->getContent());

        // minus end of lines, do not count PHP_EOL as characters
        $endOfLineCount = substr_count($currentToken->getContent(), PHP_EOL);
        $lineLength -= $endOfLineCount;

        // compute from here to end of line
        $currentPosition = $startPosition + 1;

        while (! $this->isEndOFArgumentsLine($tokens, $currentPosition)) {
            $lineLength += strlen($tokens[$currentPosition]->getContent());
            ++$currentPosition;
        }

        return $lineLength;
    }

    public function breakItems(BlockStartAndEndInfo $blockStartAndEndInfo, Tokens $tokens): void
    {
        // 1. break after arguments opening
        $this->insertNewlineAfterOpeningIfNeeded($tokens, $blockStartAndEndInfo->getStart());

        // 2. break before arguments closing
        $this->insertNewlineBeforeClosingIfNeeded($tokens, $blockStartAndEndInfo->getEnd());

        for ($i = $blockStartAndEndInfo->getStart(); $i < $blockStartAndEndInfo->getEnd(); ++$i) {
            $currentToken = $tokens[$i];

            $i = $this->tokenSkipper->skipBlocks($tokens, $i);

            // 3. new line after each comma ",", instead of just space
            if ($currentToken->getContent() === ',') {
                $tokens->ensureWhitespaceAtIndex($i + 1, 0, $this->newlineIndentWhitespace);
            }
        }
    }

    public function inlineItems(int $endPosition, Tokens $tokens, int $currentPosition): void
    {
        // replace PHP_EOL with " "
        for ($i = $currentPosition; $i < $endPosition; ++$i) {
            $currentToken = $tokens[$i];

            $i = $this->tokenSkipper->skipBlocks($tokens, $i);
            if (! $currentToken->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $previousToken = $tokens[$i - 1];
            $nextToken = $tokens[$i + 1];

            // @todo make dynamic by type? what about arrays?
            if ($previousToken->getContent() === '(' || $nextToken->getContent() === ')') {
                $tokens->clearAt($i);
                continue;
            }

            $tokens[$i] = new Token([T_WHITESPACE, ' ']);
        }
    }

    public function getLengthFromStartEnd(BlockStartAndEndInfo $blockStartAndEndInfo, Tokens $tokens): int
    {
        $lineLength = 0;

        // compute from function to start of line
        $currentPosition = $blockStartAndEndInfo->getStart();
        while (! Strings::startsWith($tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $lineLength += strlen($tokens[$currentPosition]->getContent());
            --$currentPosition;
        }

        // get spaces to first line
        $lineLength += strlen($tokens[$currentPosition]->getContent());

        // get length from start of function till end of arguments - with spaces as one
        $currentPosition = $blockStartAndEndInfo->getStart();
        while ($currentPosition < $blockStartAndEndInfo->getEnd()) {
            $currentToken = $tokens[$currentPosition];
            if ($currentToken->isGivenKind(T_WHITESPACE)) {
                ++$lineLength;
                ++$currentPosition;
                continue;
            }

            $lineLength += strlen($tokens[$currentPosition]->getContent());
            ++$currentPosition;
        }

        // get length from end or arguments to first line break
        $currentPosition = $blockStartAndEndInfo->getEnd();
        while (! Strings::startsWith($tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $currentToken = $tokens[$currentPosition];

            $lineLength += strlen($currentToken->getContent());
            ++$currentPosition;
        }

        return $lineLength;
    }

    public function isEndOFArgumentsLine(Tokens $tokens, int $position): bool
    {
        if (Strings::startsWith($tokens[$position]->getContent(), PHP_EOL)) {
            return true;
        }

        return $tokens[$position]->isGivenKind(CT::T_USE_LAMBDA);
    }

    public function insertNewlineAfterOpeningIfNeeded(Tokens $tokens, int $arrayStartIndex): void
    {
        if ($tokens[$arrayStartIndex + 1]->isGivenKind(T_WHITESPACE)) {
            $tokens->ensureWhitespaceAtIndex($arrayStartIndex + 1, 0, $this->newlineIndentWhitespace);
            return;
        }

        $tokens->ensureWhitespaceAtIndex($arrayStartIndex, 1, $this->newlineIndentWhitespace);
    }

    public function insertNewlineBeforeClosingIfNeeded(Tokens $tokens, int $arrayEndIndex): void
    {
        if ($tokens[$arrayEndIndex]->isGivenKind(T_WHITESPACE)) {
            $tokens->ensureWhitespaceAtIndex($arrayEndIndex, 0, $this->closingBracketNewlineIndentWhitespace);
            return;
        }

        $tokens->ensureWhitespaceAtIndex($arrayEndIndex + 1, 0, $this->closingBracketNewlineIndentWhitespace);
    }
}

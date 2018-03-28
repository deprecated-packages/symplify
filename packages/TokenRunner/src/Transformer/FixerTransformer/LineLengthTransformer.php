<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Transformer\FixerTransformer;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
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
        int $startPosition,
        int $endPosition,
        Tokens $tokens,
        int $currentPosition
    ): void {
        // @todo automate in some way
        $this->prepareIndentWhitespaces($tokens, $startPosition);

        $firstLineLength = $this->getFirstLineLength($startPosition, $tokens);
        if ($firstLineLength > $this->configuration->getMaxLineLength()) {
            $this->breakItems($startPosition, $endPosition, $tokens);
            return;
        }

        $fullLineLength = $this->getLengthFromStartEnd($startPosition, $endPosition, $tokens);
        if ($fullLineLength <= $this->configuration->getMaxLineLength()) {
            $this->inlineItems($endPosition, $tokens, $currentPosition);
            return;
        }
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $arrayStartIndex, $this->configuration);

        $this->indentWhitespace = str_repeat($this->configuration->getIndent(), $indentLevel + 1);
        $this->closingBracketNewlineIndentWhitespace = $this->configuration->getLineEnding() . str_repeat(
            $this->configuration->getIndent(),
            $indentLevel
        );
        $this->newlineIndentWhitespace = $this->configuration->getLineEnding() . $this->indentWhitespace;
    }

    private function getFirstLineLength(int $startPosition, Tokens $tokens): int
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

    private function breakItems(int $startPosition, int $endPosition, Tokens $tokens): void
    {
        // 1. break after arguments opening
        $tokens->ensureWhitespaceAtIndex($startPosition + 1, 0, $this->newlineIndentWhitespace);

        // 2. break before arguments closing
        $tokens->ensureWhitespaceAtIndex($endPosition + 1, 0, $this->closingBracketNewlineIndentWhitespace);

        for ($i = $startPosition; $i < $endPosition; ++$i) {
            $currentToken = $tokens[$i];

            $i = $this->tokenSkipper->skipBlocks($tokens, $i);

            // 3. new line after each comma ",", instead of just space
            if ($currentToken->getContent() === ',') {
                $tokens->ensureWhitespaceAtIndex($i + 1, 0, $this->newlineIndentWhitespace);
            }
        }
    }

    private function inlineItems(int $endPosition, Tokens $tokens, int $currentPosition): void
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

    private function getLengthFromStartEnd(int $startPosition, int $endPosition, Tokens $tokens): int
    {
        $lineLength = 0;

        // compute from function to start of line
        $currentPosition = $startPosition;
        while (! Strings::startsWith($tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $lineLength += strlen($tokens[$currentPosition]->getContent());
            --$currentPosition;
        }

        // get spaces to first line
        $lineLength += strlen($tokens[$currentPosition]->getContent());

        // get length from start of function till end of arguments - with spaces as one
        $currentPosition = $startPosition;
        while ($currentPosition < $endPosition) {
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
        $currentPosition = $endPosition;
        while (! Strings::startsWith($tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $currentToken = $tokens[$currentPosition];

            $lineLength += strlen($currentToken->getContent());
            ++$currentPosition;
        }

        return $lineLength;
    }

    private function isEndOFArgumentsLine(Tokens $tokens, int $position): bool
    {
        if (Strings::startsWith($tokens[$position]->getContent(), PHP_EOL)) {
            return true;
        }

        return $tokens[$position]->isGivenKind(CT::T_USE_LAMBDA);
    }
}

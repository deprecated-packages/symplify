<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;

final class BreakNewInstanceArgumentsFixer implements DefinedFixerInterface
{
    /**
     * @todo add as param binding?
     * @todo possibly wrap to a configuration service
     * @var int
     */
    private const LINE_LENGTH = 120;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var IndentDetector
     */
    private $indentDetector;

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

    public function __construct(IndentDetector $indentDetector, WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->indentDetector = $indentDetector;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('New instance arguments should be on the same/standalone line to fit line length.', [
            new CodeSample(
                '<?php
$someObject = new SomeClass($superLongArguments, $anotherLongArguments, $andLittleMore);
'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_NEW, '(', T_STRING, ')']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse($tokens->toArray(), true);

        foreach ($reversedTokens as $position => $token) {
            if (! $token->isGivenKind(T_NEW)) {
                continue;
            }

            $startBracketPosition = $tokens->getNextTokenOfKind($position, ['(']);
            if ($startBracketPosition === null) {
                continue;
            }

            // @todo: decouple som smart BlockStartEndFinder, where there is no need to seek
            // the type Tokens::BLOCK_TYPE_PARENTHESIS_BRACE manually, I never get it from the name
            $endBracketPosition = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startBracketPosition);

            // no arguments => skip
            if (($endBracketPosition - $startBracketPosition) <= 1) {
                continue;
            }

            $this->fixStartPositionToEndPosition($startBracketPosition, $endBracketPosition, $tokens, $position);
        }
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    /**
     * First steps to general fixer
     */
    private function fixStartPositionToEndPosition(
        int $startPosition,
        int $endPosition,
        Tokens $tokens,
        int $currentPosition
    ): void {
        // @todo automate in some way
        $this->prepareIndentWhitespaces($tokens, $startPosition);

        $firstLineLength = $this->getFirstLineLength($startPosition, $tokens);
        if ($firstLineLength > self::LINE_LENGTH) {
            $this->breakItems($startPosition, $endPosition, $tokens);
            return;
        }

        $fullLineLength = $this->getLengthFromStartEnd($startPosition, $endPosition, $tokens);
        if ($fullLineLength <= self::LINE_LENGTH) {
            $this->inlineItems($startPosition, $endPosition, $tokens, $currentPosition);
            return;
        }
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        $indentLevel = $this->indentDetector->detectOnPosition(
            $tokens,
            $arrayStartIndex,
            $this->whitespacesFixerConfig
        );
        $indentWhitespace = $this->whitespacesFixerConfig->getIndent();
        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();

        $this->indentWhitespace = str_repeat($indentWhitespace, $indentLevel + 1);
        $this->closingBracketNewlineIndentWhitespace = $lineEnding . str_repeat($indentWhitespace, $indentLevel);
        $this->newlineIndentWhitespace = $lineEnding . $this->indentWhitespace;
    }

    /**
     * @abstractable
     */
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
        while (! Strings::startsWith($tokens[$currentPosition]->getContent(), PHP_EOL)) {
            $lineLength += strlen($tokens[$currentPosition]->getContent());
            ++$currentPosition;
        }

        return $lineLength;
    }

    /**
     * @abstractable
     */
    private function breakItems(int $startPosition, int $endPosition, Tokens $tokens): void
    {
        // 1. break after arguments opening
        $tokens->ensureWhitespaceAtIndex($startPosition + 1, 0, $this->newlineIndentWhitespace);

        // 2. break before arguments closing
        $tokens->ensureWhitespaceAtIndex($endPosition + 1, 0, $this->closingBracketNewlineIndentWhitespace);

        for ($i = $startPosition; $i < $endPosition; ++$i) {
            $currentToken = $tokens[$i];

            // 3. new line after each comma ",", instead of just space
            if ($currentToken->getContent() === ',') {
                $tokens->ensureWhitespaceAtIndex($i + 1, 0, $this->newlineIndentWhitespace);
            }
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

    private function inlineItems(int $startPosition, int $endPosition, Tokens $tokens, int $currentPosition): void
    {
        // replace PHP_EOL with " "
        for ($i = $currentPosition; $i < $endPosition; ++$i) {
            $currentToken = $tokens[$i];

            if (! $currentToken->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $previousToken = $tokens[$i - 1];
            $nextToken = $tokens[$i + 1];

            // @todo make dynamic by type
            if ($previousToken->getContent() === '(' || $nextToken->getContent() === ')') {
                $tokens->clearAt($i);
                continue;
            }

            $tokens[$i] = new Token([T_WHITESPACE, ' ']);
        }
    }
}

<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\MethodCallWrapper;

final class BreakMethodCallsFixer implements DefinedFixerInterface, WhitespacesAwareFixerInterface
{
    /**
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

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Method call parameters should be on the same/standalone line to fit line length.', [
            new CodeSample(
                '<?php
$someClass = new SomeClass;
$someClass->someMethod($superLongArgument, $superLongArgument, $superLongArgument, $superLongArgument);
}'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_STRING, ',', T_OBJECT_OPERATOR, ')', '(']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse($tokens->toArray(), true);

        foreach ($reversedTokens as $position => $token) {
            if (! $this->isMethodCall($tokens, $token, $position)) {
                continue;
            }

            $this->fixMethodCall($position, $tokens);
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

    public function setWhitespacesConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->indentDetector = IndentDetector::createFromWhitespacesFixerConfig($whitespacesFixerConfig);
    }

    private function fixMethodCall(int $position, Tokens $tokens): void
    {
        $methodCallWrapper = MethodCallWrapper::createFromTokensAndPosition($tokens, $position);
        if ($methodCallWrapper->getFirstLineLength() > self::LINE_LENGTH) {
            $this->breakMethodCallParameters($methodCallWrapper, $tokens, $position);
            return;
        }

        if ($methodCallWrapper->getLineLengthToEndOfArguments() <= self::LINE_LENGTH) {
            $this->inlineMethodCallParameters($methodCallWrapper, $tokens, $position);
            return;
        }
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $arrayStartIndex);
        $indentWhitespace = $this->whitespacesFixerConfig->getIndent();
        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();

        $this->indentWhitespace = str_repeat($indentWhitespace, $indentLevel + 1);
        $this->closingBracketNewlineIndentWhitespace = $lineEnding . str_repeat($indentWhitespace, $indentLevel);
        $this->newlineIndentWhitespace = $lineEnding . $this->indentWhitespace;
    }

    private function breakMethodCallParameters(
        MethodCallWrapper $methodCallWrapper,
        Tokens $tokens,
        int $position
    ): void {
        $this->prepareIndentWhitespaces($tokens, $position);

        $start = $methodCallWrapper->getArgumentsBracketStart();
        $end = $methodCallWrapper->getArgumentsBracketEnd();

        // 1. break after arguments opening
        $tokens->ensureWhitespaceAtIndex($start + 1, 0, $this->newlineIndentWhitespace);

        // 2. break before arguments closing
        $tokens->ensureWhitespaceAtIndex($end + 1, 0, $this->closingBracketNewlineIndentWhitespace);

        for ($i = $start; $i < $end; ++$i) {
            $currentToken = $tokens[$i];

            // 3. new line after each comma ",", instead of just space
            if ($currentToken->getContent() === ',') {
                $tokens->ensureWhitespaceAtIndex($i + 1, 0, $this->newlineIndentWhitespace);
            }
        }
    }

    private function inlineMethodCallParameters(
        MethodCallWrapper $methodCallWrapper,
        Tokens $tokens,
        int $position
    ): void {
        $endPosition = $methodCallWrapper->getArgumentsBracketEnd();

        // replace PHP_EOL with " "
        for ($i = $position; $i < $endPosition; ++$i) {
            $currentToken = $tokens[$i];

            $i = TokenSkipper::skipBlocks($tokens, $i);
            if (! $currentToken->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $previousToken = $tokens[$i - 1];
            $nextToken = $tokens[$i + 1];
            if ($previousToken->getContent() === '(' || $nextToken->getContent() === ')') {
                $tokens->clearAt($i);
                continue;
            }

            $tokens[$i] = new Token([T_WHITESPACE, ' ']);
        }
    }

    private function isMethodCall(Tokens $tokens, Token $token, int $position): bool
    {
        if (! $token->isGivenKind(T_STRING)) {
            return false;
        }

        // is "someCall("?
        $next = $tokens->getNextMeaningfulToken($position);
        if (! $tokens[$next]->equals('(')) {
            return false;
        }

        // is "(->|::|function )someCall"?
        $functionNamePrefix = $tokens->getPrevMeaningfulToken($position);

        return $tokens[$functionNamePrefix]->isGivenKind([T_DOUBLE_COLON, T_OBJECT_OPERATOR, T_FUNCTION]);
    }
}

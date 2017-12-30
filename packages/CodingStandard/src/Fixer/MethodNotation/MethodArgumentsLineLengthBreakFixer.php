<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\MethodNotation;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\TokenRunner\Wrapper\FixerWrapper\MethodWrapper;

final class MethodArgumentsLineLengthBreakFixer implements FixerInterface, DefinedFixerInterface, WhitespacesAwareFixerInterface
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

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Arguments should be on the same/standalone line to fit line length.', [
            new CodeSample(
                '<?php
class SomeClass
{
    public function someMethod(SuperLongArguments $superLongArguments, AnotherLongArguments $anotherLongArguments)
    {
    }
}'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_FUNCTION, ',']);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var Token[] $reversedTokens */
        $reversedTokens = array_reverse($tokens->toArray(), true);

        foreach ($reversedTokens as $position => $token) {
            if (! $token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $this->fixMethod($position, $tokens);
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

    /**
     * Maybe include indent fixer and run before
     * it to delegate spaces indentation
     */
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

    private function fixMethod(int $position, Tokens $tokens): void
    {
        $methodWrapper = MethodWrapper::createFromTokensAndPosition($tokens, $position);
        $firstLineLength = $methodWrapper->getFirstLineLength();

        if ($firstLineLength <= self::LINE_LENGTH) {
            return;
        }

        $this->prepareIndentWhitespaces($tokens, $position);

        $start = $methodWrapper->getArgumentsBracketStart();
        $end = $methodWrapper->getArgumentsBracketEnd();

        // @todo use whitespace config
        $breakToken = new Token([T_WHITESPACE, PHP_EOL]);

        // 1. break after arguments opening
        $tokens->insertAt($start + 1, [$breakToken]);

        // 2. break before arguments closing
        $tokens->insertAt($end + 1, [$breakToken]);

        for ($i = $start; $i < $end; ++$i) {
            $currentToken = $tokens[$i];

            // 3. new line after each comma ",", instead of just space
            if ($currentToken->getContent() === ',') {
                $tokens[$i + 1] = new Token([T_WHITESPACE, PHP_EOL]);
            }
        }
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        if ($this->indentWhitespace) {
            return;
        }

        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $arrayStartIndex);
        $indentWhitespace = $this->whitespacesFixerConfig->getIndent();
        $lineEnding = $this->whitespacesFixerConfig->getLineEnding();

        $this->indentWhitespace = str_repeat($indentWhitespace, $indentLevel + 1);
        $this->closingBracketNewlineIndentWhitespace = $lineEnding . str_repeat($indentWhitespace, $indentLevel);
        $this->newlineIndentWhitespace = $lineEnding . $this->indentWhitespace;
    }
}

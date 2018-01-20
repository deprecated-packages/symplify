<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapper;

final class BreakArrayListFixer implements DefinedFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var int
     */
    private const LINE_LENGTH = 120;

    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

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
        return new FixerDefinition('Array items should be on same/standalone line to fit line length.', [
            new CodeSample(
                '<?php
$array = ["loooooooooooooooooooooooooooooooongArraaaaaaaaaaay", "looooooooooooooooooooooooooooooooongArraaaaaaaaaaay"];'
            ),
        ]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isTokenKindFound('=');
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $position => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $arrayTokensAnalyzer = ArrayWrapper::createFromTokensArrayStartPosition($tokens, $position);
            if ($arrayTokensAnalyzer->isAssociativeArray()) {
                continue;
            }

            $this->fixArray($position, $tokens, $arrayTokensAnalyzer);
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

    private function fixArray(int $position, Tokens $tokens, ArrayWrapper $arrayWrapper): void
    {
        if ($arrayWrapper->getFirstLineLength() > self::LINE_LENGTH) {
            $this->breakArrayListItems($arrayWrapper, $tokens, $position);
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

    private function breakArrayListItems(ArrayWrapper $arrayWrapper, Tokens $tokens, int $position): void
    {
        $this->prepareIndentWhitespaces($tokens, $position);

        $start = $arrayWrapper->getStartIndex();
        $end = $arrayWrapper->getEndIndex();

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
}

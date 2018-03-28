<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\LineLength;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;
use Symplify\TokenRunner\Configuration\Configuration;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapper;
use Symplify\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;

final class BreakArrayListFixer implements DefinedFixerInterface
{
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

    /**
     * @var ArrayWrapperFactory
     */
    private $arrayWrapperFactory;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Configuration $configuration,
        ArrayWrapperFactory $arrayWrapperFactory,
        IndentDetector $indentDetector,
        WhitespacesFixerConfig $whitespacesFixerConfig
    ) {
        $this->arrayWrapperFactory = $arrayWrapperFactory;
        $this->indentDetector = $indentDetector;
        $this->configuration = $configuration;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

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

            $arrayWrapper = $this->arrayWrapperFactory->createFromTokensArrayStartPosition($tokens, $position);
            if ($arrayWrapper->isAssociativeArray()) {
                continue;
            }

            $this->fixArray($position, $tokens, $arrayWrapper);
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

    private function fixArray(int $position, Tokens $tokens, ArrayWrapper $arrayWrapper): void
    {
        if ($arrayWrapper->getFirstLineLength() > $this->configuration->getMaxLineLength()) {
            $this->breakArrayListItems($arrayWrapper, $tokens, $position);
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

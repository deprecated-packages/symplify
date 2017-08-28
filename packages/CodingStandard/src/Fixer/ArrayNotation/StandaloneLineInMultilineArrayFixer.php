<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Tokenizer\ArrayTokensAnalyzer;
use Symplify\CodingStandard\Tokenizer\IndentDetector;
use Symplify\CodingStandard\Tokenizer\TokenSkipper;

final class StandaloneLineInMultilineArrayFixer implements DefinedFixerInterface, WhitespacesAwareFixerInterface
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
     * @var bool
     */
    private $isOldArray = false;

    /**
     * @var string
     */
    private $indentWhitespace;

    /**
     * @var string
     */
    private $newlineIndentWhitespace;

    /**
     * @var IndentDetector
     */
    private $indentDetector;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Indexed PHP arrays with 2 and more items should have 1 item per line.',
            [
                new CodeSample(
                    '<?php
$values = [1 => \'hey\', 2 => \'hello\'];'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isTokenKindFound(T_DOUBLE_ARROW);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $arrayTokensAnalyzer = ArrayTokensAnalyzer::createFromTokensArrayStartPosition($tokens, $index);
            $this->isOldArray = $arrayTokensAnalyzer->isOldArray();

            if (! $arrayTokensAnalyzer->isAssociativeArray()) {
                continue;
            }

            $this->fixArray($tokens, $arrayTokensAnalyzer);
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

    private function fixArray(Tokens $tokens, ArrayTokensAnalyzer $arrayTokensAnalyzer): void
    {
        $itemCount = $arrayTokensAnalyzer->getItemCount();
        if ($itemCount <= 1) {
            return;
        }

        $this->prepareIndentWhitespaces($tokens, $arrayTokensAnalyzer->getStartIndex());

        for ($i = $arrayTokensAnalyzer->getEndIndex() - 1; $i >= $arrayTokensAnalyzer->getStartIndex(); --$i) {
            $i = TokenSkipper::skipBlocksReversed($tokens, $i);

            $token = $tokens[$i];

            if (! $token->equalsAny([',', T_END_HEREDOC])) { // item separator or comment behind it
                continue;
            }

            $nextToken = $tokens[$i + 1];

            $nextNextToken = $tokens[$i + 2];
            // if next token is just space, turn it to newline
            if ($nextToken->isWhitespace(' ') && ! $nextNextToken->isComment()) {
                $tokens->ensureWhitespaceAtIndex($i + 1, 0, $this->newlineIndentWhitespace);
                ++$i;
            }
        }

        $this->insertNewlineBeforeClosingIfNeeded($tokens, $arrayTokensAnalyzer->getEndIndex());
        $this->insertNewlineAfterOpeningIfNeeded($tokens, $arrayTokensAnalyzer->getStartIndex());
    }

    private function insertNewlineAfterOpeningIfNeeded(Tokens $tokens, int $arrayStartIndex): void
    {
        $offset = $this->isOldArray ? 1 : 0;
        if ($tokens[$arrayStartIndex + $offset + 1]->isGivenKind(T_WHITESPACE)) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($arrayStartIndex + $offset, 1, $this->newlineIndentWhitespace);
    }

    private function insertNewlineBeforeClosingIfNeeded(Tokens $tokens, int $arrayEndIndex): void
    {
        if ($tokens[$arrayEndIndex - 1]->isGivenKind(T_WHITESPACE)) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($arrayEndIndex, 0, $this->whitespacesFixerConfig->getLineEnding());
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        $indentLevel = $this->indentDetector->detectOnPosition($tokens, $arrayStartIndex);

        $this->indentWhitespace = str_repeat($this->whitespacesFixerConfig->getIndent(), $indentLevel + 1);
        $this->newlineIndentWhitespace = $this->whitespacesFixerConfig->getLineEnding() . $this->indentWhitespace;
    }
}

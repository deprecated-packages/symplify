<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;

final class StandaloneLineInMultilineArrayFixer implements DefinedFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var int[]
     */
    private const ARRAY_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var ?int
     */
    private $arrayEndIndex;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Indexed PHP arrays should have 1 item per line.',
            [
                new CodeSample(
                    '<?php
$values = [ 1 => \'hey\', 2 => \'hello\' ];'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_TOKENS);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(self::ARRAY_TOKENS)) {
                continue;
            }

            $this->arrayEndIndex = $this->detectArrayEndPosition($tokens, $index);

            if (! $this->isAssociativeArray($tokens, $index, $this->arrayEndIndex)) {
                continue;
            }

            $this->fixArray($tokens, $index);
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
        // run before IndentationTypeFixer
        return 70;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    private function fixArray(Tokens $tokens, int $index): void
    {
        $arrayStartIndex = $index;
        $arrayEndIndex = $this->arrayEndIndex;

        for ($i = $arrayEndIndex; $i >= $arrayStartIndex; --$i) {
            $token = $tokens[$i];

            if ($token->getContent() !== ',') {
                continue;
            }

            $nextToken = $tokens[$i + 1];
            if ($nextToken->getContent() === ' ') {
                $tokens[$i + 1] = new Token([T_WHITESPACE, $this->whitespacesFixerConfig->getLineEnding()]);
                ++$i;
            }
        }

        $this->insertNewlineBeforeClosingIfNeeded($tokens, $arrayEndIndex);
        $this->insertNewlineAfterOpeningIfNeeded($tokens, $arrayStartIndex);
    }

    private function detectArrayEndPosition(Tokens $tokens, int $startIndex): int
    {
        if ($tokens[$startIndex]->isGivenKind(T_ARRAY)) {
            $startIndex = $tokens->getNextTokenOfKind($startIndex, ['(']);

            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        }

        return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
    }

    private function isAssociativeArray(Tokens $tokens, int $startIndex, int $endIndex): bool
    {
        for ($i = $startIndex; $i <= $endIndex; ++$i) {
            $token = $tokens[$i];

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                return true;
            }
        }

        return false;
    }

    private function insertNewlineAfterOpeningIfNeeded(Tokens $tokens, int $arrayStartIndex): void
    {
        if ($tokens[$arrayStartIndex + 1]->isGivenKind(T_WHITESPACE)) {
            return;
        }

        $tokens[$arrayStartIndex]->clear();
        $tokens->insertAt($arrayStartIndex, [
            new Token([CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']),
            new Token([T_WHITESPACE, $this->whitespacesFixerConfig->getLineEnding()]),
        ]);
    }

    private function insertNewlineBeforeClosingIfNeeded(Tokens $tokens, int $arrayEndIndex): void
    {
        if ($tokens[$arrayEndIndex - 1]->isGivenKind(T_WHITESPACE)) {
            return;
        }

        $tokens[$arrayEndIndex]->clear();
        $tokens->insertAt($arrayEndIndex, [
            new Token([T_WHITESPACE, $this->whitespacesFixerConfig->getLineEnding()]),
            new Token([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']),
        ]);
    }
}

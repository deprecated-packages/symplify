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
use Symplify\CodingStandard\Tokenizer\ArrayTokensAnalyzer;

final class StandaloneLineInMultilineArrayFixer implements DefinedFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var int[]
     */
    private const ARRAY_CLOSING_TOKENS = [')', CT::T_ARRAY_SQUARE_BRACE_CLOSE];

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
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS + [T_DOUBLE_ARROW]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $arrayTokensAnalyzer = new ArrayTokensAnalyzer($tokens, $index);
            $arrayEndIndex = $arrayTokensAnalyzer->getEndIndex();
            $this->isOldArray = $arrayTokensAnalyzer->isOldArray();

            if (! $this->isAssociativeArray($tokens, $index, $arrayEndIndex)) {
                continue;
            }

            $this->fixArray($tokens, $index, $arrayEndIndex);
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
    }

    private function fixArray(Tokens $tokens, int $arrayStartIndex, int $arrayEndIndex): void
    {
        $itemCount = $this->getItemCount($tokens, $arrayEndIndex, $arrayStartIndex);
        if ($itemCount <= 1) {
            return;
        }

        $this->prepareIndentWhitespaces($tokens, $arrayStartIndex);

        for ($i = $arrayEndIndex - 1; $i >= $arrayStartIndex; --$i) {
            $token = $tokens[$i];

            $i = $this->skipBlocks($tokens, $token, $i);

            if ($token->getContent() !== ',') { // item separator
                continue;
            }

            $nextToken = $tokens[$i + 1];
            // if next token is just space, turn it to newline
            if ($nextToken->isWhitespace(' ')) {
                $tokens[$i + 1] = new Token([T_WHITESPACE, $this->newlineIndentWhitespace]);
                ++$i;
            }
        }

        $this->insertNewlineBeforeClosingIfNeeded($tokens, $arrayEndIndex);
        $this->insertNewlineAfterOpeningIfNeeded($tokens, $arrayStartIndex);
    }

    /**
     * @todo Move to ArrayTokensAnalyzer class
     */
    private function isAssociativeArray(Tokens $tokens, int $startIndex, int $endIndex): bool
    {
        $isDivedInAnotherArray = false;

        for ($i = $startIndex + 1; $i <= $endIndex - 1; ++$i) {
            $token = $tokens[$i];

            if ($isDivedInAnotherArray === false && $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                $isDivedInAnotherArray = true;
            } elseif ($isDivedInAnotherArray && $token->isGivenKind(self::ARRAY_CLOSING_TOKENS)) {
                $isDivedInAnotherArray = false;
            }

            // do not process dived arrays in this run
            if ($isDivedInAnotherArray) {
                continue;
            }

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                return true;
            }
        }

        return false;
    }

    private function insertNewlineAfterOpeningIfNeeded(Tokens $tokens, int $arrayStartIndex): void
    {
        $offset = $this->isOldArray ? 1 : 0;
        if ($tokens[$arrayStartIndex + $offset + 1]->isGivenKind(T_WHITESPACE)) {
            return;
        }

        $tokens[$arrayStartIndex + $offset]->clear();
        $tokens->insertAt($arrayStartIndex + $offset, [
            $this->isOldArray ? new Token('(') : new Token([CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']),
            new Token([T_WHITESPACE, $this->newlineIndentWhitespace]),
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
            $this->isOldArray ? new Token(')') : new Token([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']),
        ]);
    }

    /**
     * @todo Move to ArrayTokensAnalyzer class
     */
    private function getItemCount(Tokens $tokens, int $arrayEndIndex, int $arrayStartIndex): int
    {
        $itemCount = 0;
        for ($i = $arrayEndIndex; $i >= $arrayStartIndex; --$i) {
            $token = $tokens[$i];
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                ++$itemCount;
            }
        }

        return $itemCount;
    }

    private function prepareIndentWhitespaces(Tokens $tokens, int $arrayStartIndex): void
    {
        $indentLevel = $this->getIndentLevel($tokens, $arrayStartIndex);

        $this->indentWhitespace = str_repeat($this->whitespacesFixerConfig->getIndent(), $indentLevel + 1);
        $this->newlineIndentWhitespace = $this->whitespacesFixerConfig->getLineEnding() . $this->indentWhitespace;
    }

    /**
     * @todo Move to helper class
     */
    private function getIndentLevel(Tokens $tokens, int $arrayStartIndex): int
    {
        for ($i = $arrayStartIndex; $i > 0; --$i) {
            $token = $tokens[$i];

            if ($token->isWhitespace() && $token->getContent() !== ' ') {
                return substr_count($token->getContent(), $this->whitespacesFixerConfig->getIndent());
            }
        }

        return 0;
    }

    private function skipBlocks(Tokens $tokens, Token $token, int $i): int
    {
        $tokenCountToSkipOver = 0;

        if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            // @wtf: with 3rd arg false works like "findBlockStart()"
            $blockStart = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $i, false);
            $tokenCountToSkipOver = $i - $blockStart;
        }

        if ($token->getContent() === ')') {
            $blockStart = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $i, false);
            $tokenCountToSkipOver = $i - $blockStart;
        }

        return $i - $tokenCountToSkipOver;
    }
}

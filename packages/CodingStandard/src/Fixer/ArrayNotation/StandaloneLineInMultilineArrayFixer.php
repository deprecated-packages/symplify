<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class StandaloneLineInMultilineArrayFixer implements DefinedFixerInterface
{
    /**
     * @var int[]
     */
    private const ARRAY_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var ?int
     */
    private $arrayEndIndex;

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

            if (! $this->isAssociativeArray($tokens, $index)) {
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
        // @todo: should run before indentation fixer
        return 0;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    private function fixArray(Tokens $tokens, int $index): void
    {
        $arrayStartIndex = $index;
        $arrayEndIndex = $this->arrayEndIndex;

        for ($i = $arrayStartIndex; $i <= $arrayEndIndex; ++$i) {
            $token = $tokens[$i];

            // add space after [
            // add space before ]

            if ($token->getContent() !== ',') {
                continue;
            }

            $nextToken = $tokens[$i + 1];
            if ($nextToken->getContent() === ' ') {
                $tokens[$i + 1] = new Token([T_WHITESPACE, PHP_EOL]);
            }
        }

        // insert new line after [
        $tokens[$arrayStartIndex]->clear();
        $tokens->insertAt($arrayStartIndex, [
            new Token([CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']),
            new Token([T_WHITESPACE, PHP_EOL]), // @todo: get from config
        ]);

        // insert new line before [
        $tokens[$arrayEndIndex]->clear();
        $tokens->insertAt($arrayEndIndex, [
            new Token([T_WHITESPACE, PHP_EOL]), // @todo: get from config
            new Token([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']),
        ]);
    }

    private function isAssociativeArray(Tokens $tokens, int $index): bool
    {
        $this->arrayEndIndex = null;
        $isAssociativeArray = false;

        for ($i = $index; $i <= count($tokens) - 1; ++$i) {
            $token = $tokens[$i];

            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $isAssociativeArray = true;
            }

            if ($token->isGivenKind([CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']'])) {
                $this->arrayEndIndex = $i + 2;
            }
        }

        return $isAssociativeArray && $this->arrayEndIndex;
    }
}

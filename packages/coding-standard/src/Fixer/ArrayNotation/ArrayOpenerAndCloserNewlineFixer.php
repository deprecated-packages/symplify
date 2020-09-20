<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixerTest\ArrayOpenerAndCloserNewlineFixerTest
 */
class ArrayOpenerAndCloserNewlineFixer extends AbstractArrayFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Indexed PHP array opener and closer must be indented on newline', []);
    }

    public function fixArrayOpener(Tokens $tokens, BlockInfo $blockInfo, int $index): void
    {
        if ($this->isNextTokenAlsoArrayOpener($tokens, $index)) {
            return;
        }

        // is single line? → skip
        if (! $tokens->isPartialCodeMultiline($blockInfo->getStart(), $blockInfo->getEnd())) {
            return;
        }

        // closer must run before the opener, as tokens as added by traversing up
        $this->handleArrayCloser($tokens, $blockInfo->getEnd());
        $this->handleArrayOpener($tokens, $index);
    }

    private function isNextTokenAlsoArrayOpener(Tokens $tokens, int $index): bool
    {
        $nextMeaningFullTokenPosition = $tokens->getNextMeaningfulToken($index);
        if ($nextMeaningFullTokenPosition === null) {
            return false;
        }

        $nextMeaningFullToken = $tokens[$nextMeaningFullTokenPosition];
        return $nextMeaningFullToken->isGivenKind(self::ARRAY_OPEN_TOKENS);
    }

    private function handleArrayCloser(Tokens $tokens, int $arrayCloserPosition): void
    {
        $preArrayCloserPosition = $arrayCloserPosition - 1;

        /** @var Token|null $previousCloserToken */
        $previousCloserToken = $tokens[$preArrayCloserPosition] ?? null;
        if ($previousCloserToken === null) {
            return;
        }

        // already whitespace
        if ($previousCloserToken->isGivenKind(T_WHITESPACE)) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($preArrayCloserPosition, 1, $this->whitespacesFixerConfig->getLineEnding());
    }

    private function handleArrayOpener(Tokens $tokens, int $arrayOpenerPosition): void
    {
        /** @var Token|null $nextToken */
        $nextToken = $tokens[$arrayOpenerPosition + 1] ?? null;
        if ($nextToken === null) {
            return;
        }

        // already is whitespace
        if ($nextToken->isGivenKind(T_WHITESPACE)) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($arrayOpenerPosition + 1, 0, $this->whitespacesFixerConfig->getLineEnding());
    }
}

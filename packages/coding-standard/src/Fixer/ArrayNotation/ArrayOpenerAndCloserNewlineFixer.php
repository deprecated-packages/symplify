<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixerTest\ArrayOpenerAndCloserNewlineFixerTest
 */
class ArrayOpenerAndCloserNewlineFixer extends AbstractSymplifyFixer
{
    /**
     * @var int[]
     */
    private const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function __construct(BlockFinder $blockFinder, WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->blockFinder = $blockFinder;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Indexed PHP array opener and closer must be indented on newline', []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isTokenKindFound(T_DOUBLE_ARROW);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($this->reverseTokens($tokens) as $index => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            if ($this->isNextTokenAlsoArrayOpener($tokens, $index)) {
                continue;
            }

            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);
            if ($blockInfo === null) {
                continue;
            }

            // is single line? → skip
            if (! $tokens->isPartialCodeMultiline($blockInfo->getStart(), $blockInfo->getEnd())) {
                continue;
            }

            // closer must run before the opener, as tokens as added by traversing up
            $this->handleArrayCloser($tokens, $blockInfo->getEnd());
            $this->handleArrayOpener($tokens, $index);
        }
    }

    public function getPriority(): int
    {
        // to handle the indent
        return $this->getPriorityBefore(ArrayIndentationFixer::class);
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

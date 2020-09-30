<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Spacing;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\MethodChainingNewlineFixer\MethodChainingNewlineFixerTest
 */
final class MethodChainingNewlineFixer extends AbstractSymplifyFixer
{
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    public function __construct(WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Makes each chain method call on own line', []);
    }

    public function getPriority(): int
    {
        return $this->getPriorityBefore(MethodChainingIndentationFixer::class);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_OBJECT_OPERATOR]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        // function arguments, function call parameters, lambda use()
        for ($index = 1, $count = count($tokens); $index < $count; ++$index) {
            $currentToken = $tokens[$index];
            if (! $currentToken->isGivenKind(T_OBJECT_OPERATOR)) {
                continue;
            }

            if (! $this->shouldPrefixNewline($tokens, $index)) {
                continue;
            }

            $tokens->ensureWhitespaceAtIndex($index, 0, $this->whitespacesFixerConfig->getLineEnding());
        }
    }

    private function shouldPrefixNewline(Tokens $tokens, int $objectOperatorIndex): bool
    {
        for ($i = $objectOperatorIndex; $i >= 0; --$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];

            if ($currentToken->equals(')')) {
                if ($this->isDoubleBracket($tokens, $i)) {
                    return false;
                }

                if ($this->isPartOfMethodCallOrArray($tokens, $i)) {
                    return false;
                }

                // all good, there is a newline
                return ! $tokens->isPartialCodeMultiline($i, $objectOperatorIndex);
            }

            if ($currentToken->isGivenKind([T_NEW, T_VARIABLE])) {
                return false;
            }

            if ($currentToken->getContent() === '(') {
                return false;
            }
        }

        return false;
    }

    private function isDoubleBracket(Tokens $tokens, int $position): bool
    {
        /** @var int $nextTokenPosition */
        $nextTokenPosition = $tokens->getNextNonWhitespace($position);

        /** @var Token $nextToken */
        $nextToken = $tokens[$nextTokenPosition];
        return $nextToken->getContent() === ')';
    }

    /**
     * Matches e.g.:
     * - someMethod($this->some()->method())
     * - [$this->some()->method()]
     * - ' ' . $this->some()->method()
     */
    private function isPartOfMethodCallOrArray(Tokens $tokens, int $position): bool
    {
        for ($i = $position; $i >= 0; --$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];

            // break
            if ($currentToken->isWhitespace() && Strings::contains($currentToken->getContent(), "\n")) {
                return false;
            }

            if ($currentToken->isGivenKind([CT::T_ARRAY_SQUARE_BRACE_OPEN, T_ARRAY])) {
                return true;
            }

            if ($currentToken->getContent() === '(' || $currentToken->getContent() === '.') {
                if ($position === $i + 1) {
                    // skip ()
                    continue;
                }

                return true;
            }
        }

        return false;
    }
}

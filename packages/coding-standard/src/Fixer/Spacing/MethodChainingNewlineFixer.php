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

                if ($this->isPreceededByFuncCall($tokens, $i)) {
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
            if ($this->isNewlineToken($currentToken)) {
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

    /**
     * Matches e..g:
     * - app()->some()
     */
    private function isPreceededByFuncCall(Tokens $tokens, int $position): bool
    {
        for ($i = $position; $i >= 0; --$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];

            if ($currentToken->getContent() === '(') {
                $previousToken = $this->getPreviousToken($tokens, $position);
                if (! $previousToken->isGivenKind(T_STRING)) {
                    return false;
                }

                $previousPreviousToken = $this->getPreviousToken($tokens, $position - 1);

                // is a function
                return $previousPreviousToken->isGivenKind([T_RETURN, T_DOUBLE_COLON]);
            }

            if ($this->isNewlineToken($currentToken)) {
                return false;
            }
        }

        return false;
    }

    private function isNewlineToken(Token $currentToken): bool
    {
        if (! $currentToken->isWhitespace()) {
            return false;
        }

        return Strings::contains($currentToken->getContent(), "\n");
    }

    private function getPreviousToken(Tokens $tokens, int $position): ?Token
    {
        $previousMeaningfulTokenPosition = $tokens->getPrevMeaningfulToken($position - 1);
        if ($previousMeaningfulTokenPosition === null) {
            return null;
        }

        return $tokens[$previousMeaningfulTokenPosition];
    }
}

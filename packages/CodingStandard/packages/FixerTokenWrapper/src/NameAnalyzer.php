<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NameAnalyzer
{
    public static function isImportableName(Tokens $tokens, Token $token, int $index): bool
    {
        if (! $token->isGivenKind(T_STRING)) {
            return false;
        }

        // already part of another namespaced name
        if ($tokens[$index + 1]->isGivenKind(T_NS_SEPARATOR)) {
            return false;
        }

        if ($tokens[$index - 2]->isGivenKind(T_NAMESPACE)) {
            // namespace: namespace "SomeName"
            return false;
        }

        if ($tokens[$index - 2]->isGivenKind(T_USE)) {
            // use statement: use "SomeName"
            return false;
        }

        if (! $tokens[$index - 1]->isGivenKind(T_NS_SEPARATOR)
            && ! $tokens[$index + 1]->isGivenKind(T_NS_SEPARATOR)) {
            // cannot be bare name SomeName - use slash before/after, only \SomeName or SomeName\
            return false;
        }

        // one is in use statement, how to detect it?
        $currentIndex = $index;
        while ($tokens[$currentIndex]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            if ($tokens[$currentIndex - 2]->isGivenKind(T_USE)) {
                // namespace: namespace "SomeName"
                return false;
            }

            --$currentIndex;
        }

        return true;
    }
}

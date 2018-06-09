<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming\Name;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NameAnalyzer
{
    public function isImportableNameToken(Tokens $tokens, Token $token, int $index): bool
    {
        if (! $token->isGivenKind(T_STRING)) {
            return false;
        }

        $previousToken = $tokens[$index - 2];
        if ($previousToken->getContent() === 'function') {
            return false;
        }

        // already part of another namespaced name
        if ($tokens[$index + 1]->isGivenKind(T_NS_SEPARATOR)) {
            return false;
        }

        if (! $tokens[$index - 1]->isGivenKind(T_NS_SEPARATOR)
            && ! $tokens[$index + 1]->isGivenKind(T_NS_SEPARATOR)) {
            // cannot be bare name SomeName - use slash before/after, only \SomeName or SomeName\
            return false;
        }

        // is part of use/namespace statement
        return ! $this->isPartOfUseOrNamespaceStatement($tokens, $index);
    }

    private function isPartOfUseOrNamespaceStatement(Tokens $tokens, int $index): bool
    {
        $currentIndex = $index;

        while ($tokens[$currentIndex]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            if ($tokens[$currentIndex - 2]->isGivenKind([T_USE, T_NAMESPACE])) {
                // use "SomeName" or namespace "SomeName"
                return true;
            }

            // use function
            if ($tokens[$currentIndex - 2]->getContent() === 'function') {
                return true;
            }

            --$currentIndex;
        }

        return false;
    }
}

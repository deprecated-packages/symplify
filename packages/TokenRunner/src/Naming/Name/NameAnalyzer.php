<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming\Name;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Naming\UseImport\UseImportsFactory;

final class NameAnalyzer
{
    public static function isImportableNameToken(Tokens $tokens, Token $token, int $index): bool
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
        $currentIndex = $index;
        while ($tokens[$currentIndex]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            if ($tokens[$currentIndex - 2]->isGivenKind([T_USE, T_NAMESPACE])) {
                // use "SomeName" or namespace "SomeName"
                return false;
            }

            if ($tokens[$currentIndex - 2]->getContent() === 'function') {
                return false;
            }

            --$currentIndex;
        }

        // is "use function" statement

        return true;
    }


    // @todo: move to name?
    public static function isPartialName(Tokens $tokens, Name $name): bool
    {
        if (Strings::startsWith($name->getName(), '\\')) {
            return false;
        }

        if (! Strings::contains($name->getName(), '\\')) {
            return false;
        }

        $useImports = (new UseImportsFactory())->createForTokens($tokens);

        foreach ($useImports as $useImport) {
            if ($useImport->startsWith($name->getName())) {
                $name->setRelatedUseImport($useImport);

                return true;
            }
        }

        return false;
    }
}

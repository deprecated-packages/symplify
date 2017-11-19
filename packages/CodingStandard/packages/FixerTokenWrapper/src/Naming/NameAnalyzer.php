<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper\Naming;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use ReflectionMethod;

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

        // is use funtion statement

        return true;
    }

    public static function isPartialName(Tokens $tokens, Name $name): bool
    {
        if (Strings::startsWith($name->getName(), '\\')) {
            return false;
        }

        if (! Strings::contains($name->getName(), '\\')) {
            return false;
        }

        /** @var int[] $importUseIndexes */
        $importUseIndexes = (new TokensAnalyzer($tokens))->getImportUseIndexes();
        if (! $importUseIndexes) {
            return false;
        }

        $namespaceUseDeclarations = self::getNamespaceUseDeclarations($tokens, $importUseIndexes);

        foreach ($namespaceUseDeclarations as $useDeclaration) {
            if (Strings::startsWith($name->getName(), $useDeclaration['shortName'])) {
                $name->setPartialUseDeclaration($useDeclaration);

                return true;
            }
        }

        return false;
    }

    /**
     * Reflection over code copying that would force many updates.
     *
     * @todo consider objectify for better API
     *
     * @param int[] $importUseIndexes
     * @return mixed[][]
     */
    private static function getNamespaceUseDeclarations(Tokens $tokens, array $importUseIndexes): array
    {
        $reflectionMethod = new ReflectionMethod(NoUnusedImportsFixer::class, 'getNamespaceUseDeclarations');
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invoke(new NoUnusedImportsFixer(), $tokens, $importUseIndexes);
    }
}

<?php declare(strict_types=1);

namespace Symplify\TokenRunner\FixerTokenWrapper\Naming;

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use ReflectionMethod;

/**
 * Mimics @see \Symplify\TokenRunner\Helper\Naming.
 */
final class ClassFqnResolver
{
    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @var string[][]
     */
    private static $namespaceUseDeclarationsPerTokens = [];

    public static function resolveForNamePosition(Tokens $tokens, int $classNameEndPosition): string
    {
        $classNameParts = [];
        $classNameParts[] = $tokens[$classNameEndPosition]->getContent();

        $previousTokenPointer = $classNameEndPosition - 1;

        while ($tokens[$previousTokenPointer]->isGivenKind([T_NS_SEPARATOR])) {
            --$previousTokenPointer;
            $classNameParts[] = $tokens[$previousTokenPointer]->getContent();
            --$previousTokenPointer;
        }

        $completeClassName = implode(self::NAMESPACE_SEPARATOR, $classNameParts);

        return self::resolveForName($tokens, $completeClassName);
    }

    public static function resolveDataFromEnd(Tokens $tokens, int $end): Name
    {
        $nameTokens = [];

        $previousTokenPointer = $end;

        while ($tokens[$previousTokenPointer]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $nameTokens[] = $tokens[$previousTokenPointer];
            --$previousTokenPointer;
        }

        /** @var Token[] $nameTokens */
        $nameTokens = array_reverse($nameTokens);
        if ($nameTokens[0]->isGivenKind(T_NS_SEPARATOR)) {
            unset($nameTokens[0]);
            // reset array keys
            $nameTokens = array_values($nameTokens);

            // move start pointer after "\"
            ++$previousTokenPointer;
        }

        if (! $tokens[$previousTokenPointer]->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
            ++$previousTokenPointer;
        }

        $name = '';
        foreach ($nameTokens as $nameToken) {
            $name .= $nameToken->getContent();
        }

        return new Name($previousTokenPointer, $end, $name, $nameTokens);
    }

    public static function resolveDataFromStart(Tokens $tokens, int $start): Name
    {
        $nameTokens = [];

        $nextTokenPointer = $start;

        while ($tokens[$nextTokenPointer]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $nameTokens[] = $tokens[$nextTokenPointer];
            ++$nextTokenPointer;
        }

        /** @var Token[] $nameTokens */
        if ($nameTokens[0]->isGivenKind(T_NS_SEPARATOR)) {
            unset($nameTokens[0]);
            // reset array keys
            $nameTokens = array_values($nameTokens);

            // move start pointer after "\"
            --$nextTokenPointer;
        }

        if (! $tokens[$nextTokenPointer]->isGivenKind([T_STRING, T_NS_SEPARATOR])) {
            --$nextTokenPointer;
        }

        $name = '';
        foreach ($nameTokens as $nameToken) {
            $name .= $nameToken->getContent();
        }

        return new Name($nextTokenPointer, $start, $name, $nameTokens);
    }

    public static function resolveForName(Tokens $tokens, string $className): string
    {
        // probably not a class name, skip
        if (ctype_lower($className[0])) {
            return $className;
        }

        $tokensAnalyzer = new TokensAnalyzer($tokens);
        $useDeclarations = self::getNamespaceUseDeclarations($tokens, $tokensAnalyzer->getImportUseIndexes());

        foreach ($useDeclarations as $name => $settings) {
            if ($className === $name) {
                return $settings['fullName'];
            }
        }

        return $className;
    }

    /**
     * Mimics @see NoUnusedImportsFixer::getNamespaceUseDeclarations().
     *
     * @param int[] $useIndexes
     * @return string[]
     */
    private static function getNamespaceUseDeclarations(Tokens $tokens, array $useIndexes): array
    {
        if (isset(self::$namespaceUseDeclarationsPerTokens[$tokens->getCodeHash()])) {
            return self::$namespaceUseDeclarationsPerTokens[$tokens->getCodeHash()];
        }

        $methodReflection = new ReflectionMethod(
            NoUnusedImportsFixer::class,
            'getNamespaceUseDeclarations'
        );

        $methodReflection->setAccessible(true);

        $namespaceUseDeclarations = $methodReflection->invoke(new NoUnusedImportsFixer(), $tokens, $useIndexes);

        self::$namespaceUseDeclarationsPerTokens[$tokens->getCodeHash()] = $namespaceUseDeclarations;

        return $namespaceUseDeclarations;
    }
}

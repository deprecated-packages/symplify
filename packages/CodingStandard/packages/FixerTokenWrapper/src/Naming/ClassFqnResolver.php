<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper\Naming;

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use ReflectionMethod;

/**
 * Mimics @see \Symplify\CodingStandard\Helper\Naming.
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

    /**
     * @param int $start Position of string of namespace separator
     *
     * Resolves from start:
     * - {\}Some\Namespace => Some\Namespace
     * - {Some}\Namespace => Some\Namespace
     *
     * @return int[]|string[]
     */
    public static function resolveDataFromStart(Tokens $tokens, int $start): array
    {
        $nameParts = [];

        $nextTokenPointer = $start;

        while ($tokens[$nextTokenPointer]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $nameParts[] = $tokens[$nextTokenPointer]->getContent();
            ++$nextTokenPointer;
        }

        return [
            'start' => $start,
            'end' => $nextTokenPointer,
            'name' => implode('', $nameParts),
            'lastPart' => $nameParts[count($nameParts) - 1]
        ];
    }

    public static function resolveForNamePosition(Tokens $tokens, int $classNameEndPosition): string
    {
        $classNameParts = [];
        $classNameParts[] = $tokens[$classNameEndPosition]->getContent();

        $previousTokenPointer = $classNameEndPosition - 1;

        while ($tokens[$previousTokenPointer]->getId() === T_NS_SEPARATOR) {
            --$previousTokenPointer;
            $classNameParts[] = $tokens[$previousTokenPointer]->getContent();
            --$previousTokenPointer;
        }

        $completeClassName = implode(self::NAMESPACE_SEPARATOR, $classNameParts);

        return self::resolveForName($tokens, $completeClassName);
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

        $namespaceUseDeclarations = $methodReflection->invoke(new NoUnusedImportsFixer, $tokens, $useIndexes);

        self::$namespaceUseDeclarationsPerTokens[$tokens->getCodeHash()] = $namespaceUseDeclarations;

        return $namespaceUseDeclarations;
    }
}

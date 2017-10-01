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
     * @var string[]
     */
    private static $namespaceUseDeclarationsPerTokens = [];

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
     * @return mixed[]
     */
    private static function getNamespaceUseDeclarations(Tokens $tokens, array $useIndexes): array
    {
        if (isset(self::$namespaceUseDeclarationsPerTokens[$tokens->getCodeHash()])) {
            return self::$namespaceUseDeclarationsPerTokens[$tokens->getCodeHash()];
        }

        $getNamespaceUseDeclarationsMethodReflection = new ReflectionMethod(
            NoUnusedImportsFixer::class,
            'getNamespaceUseDeclarations'
        );

        $getNamespaceUseDeclarationsMethodReflection->setAccessible(true);

        $namespaceUseDeclarations = $getNamespaceUseDeclarationsMethodReflection->invoke(new NoUnusedImportsFixer, $tokens, $useIndexes);

        self::$namespaceUseDeclarationsPerTokens[$tokens->getCodeHash()] = $namespaceUseDeclarations;

        return $namespaceUseDeclarations;
    }
}

<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming;

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use ReflectionMethod;

final class UseImportsFactory
{
    /**
     * @var UseImport[][]
     */
    private $cachedUseImports = [];

    /**
     * Reflection over code copying
     * that would force many updates and instability.
     *
     * @return UseImport[]
     */
    public function createForTokens(Tokens $tokens): array
    {
        if (isset($this->cachedUseImports[$tokens->getCodeHash()])) {
            return $this->cachedUseImports[$tokens->getCodeHash()];
        }

        /** @var int[] $importUseIndexes */
        $importUseIndexes = (new TokensAnalyzer($tokens))->getImportUseIndexes();
        if (! $importUseIndexes) {
            return [];
        }

        $useImports = $this->callPrivateMethod(
            NoUnusedImportsFixer::class,
            'getNamespaceUseDeclarations',
            $tokens,
            $importUseIndexes
        );

        $useImports = $this->wrapToValueObjects($useImports);

        return $this->cachedUseImports[$tokens->getCodeHash()] = $useImports;
    }

    /**
     * @param mixed ...$args
     * @return string[]
     */
    private function callPrivateMethod(string $class, string $method, ...$args): array
    {
        $reflectionMethod = new ReflectionMethod($class, $method);
        $reflectionMethod->setAccessible(true);

        $object = new $class();

        return $reflectionMethod->invoke($object, ...$args);
    }

    /**
     * @param mixed[] $useImports
     * @return UseImport[]
     */
    private function wrapToValueObjects(array $useImports): array
    {
        $valueObjects = [];
        foreach ($useImports as $useImport) {
            $valueObjects[] = new UseImport($useImport['fullName'], $useImport['shortName']);
        }

        return $valueObjects;
    }
}

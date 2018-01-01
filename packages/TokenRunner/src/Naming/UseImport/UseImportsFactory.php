<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Naming\UseImport;

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

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

        $useImports = (new PrivatesCaller())->callPrivateMethod(
            (new NoUnusedImportsFixer()),
            'getNamespaceUseDeclarations',
            $tokens,
            $importUseIndexes
        );

        return $this->cachedUseImports[$tokens->getCodeHash()] = $this->wrapToValueObjects($useImports);
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

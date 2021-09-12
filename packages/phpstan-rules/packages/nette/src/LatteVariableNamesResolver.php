<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\LattePHPStanPrinter\LatteToPhpCompiler;
use Symplify\PHPStanRules\Nette\PhpNodeVisitor\LatteVariableCollectingNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\ParentLayoutNameNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\ParentNodeAwarePhpParser;

final class LatteVariableNamesResolver
{
    public function __construct(
        private ParentNodeAwarePhpParser $parentNodeAwarePhpParser,
        private LatteToPhpCompiler $latteToPhpCompiler,
        private LatteVariableCollectingNodeVisitor $latteVariableCollectingNodeVisitor,
        private ParentLayoutNameNodeVisitor $parentLayoutNameNodeVisitor,
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $filePath): array
    {
        $compiledPhp = $this->latteToPhpCompiler->compileFilePath($filePath);

        $phpNodes = $this->parentNodeAwarePhpParser->parsePhpContent($compiledPhp);

        // resolve parent layout variables
        $parentLayoutFileName = $this->resolveParentFileNameFromPhpNodes($filePath, $phpNodes);

        $usedVariableNames = [];

        if ($parentLayoutFileName !== null) {
            $parentLayoutCompiledPhp = $this->latteToPhpCompiler->compileFilePath($parentLayoutFileName);
            $parentLayoutPhpNodes = $this->parentNodeAwarePhpParser->parsePhpContent($parentLayoutCompiledPhp);

            $parentUsedVariableNames = $this->resolveUsedVariableNamesFromPhpNodes($parentLayoutPhpNodes);
            $usedVariableNames = array_merge($usedVariableNames, $parentUsedVariableNames);
        }

        $currentUsedVariableNames = $this->resolveUsedVariableNamesFromPhpNodes($phpNodes);
        return array_merge($usedVariableNames, $currentUsedVariableNames);
    }

    /**
     * @param \PhpParser\Node[] $phpNodes
     * @return string[]
     */
    private function resolveUsedVariableNamesFromPhpNodes(array $phpNodes): array
    {
        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor($this->latteVariableCollectingNodeVisitor);
        $phpNodeTraverser->traverse($phpNodes);

        return $this->latteVariableCollectingNodeVisitor->getUsedVariableNames();
    }

    /**
     * @param Stmt[] $phpNodes
     */
    private function resolveParentFileNameFromPhpNodes(string $filePath, array $phpNodes): ?string
    {
        $phpNodeTraverser = new NodeTraverser();
        $this->parentLayoutNameNodeVisitor->setCurrentFilePath($filePath);
        $phpNodeTraverser->addVisitor($this->parentLayoutNameNodeVisitor);
        $phpNodeTraverser->traverse($phpNodes);

        return $this->parentLayoutNameNodeVisitor->getParentLayoutFileName();
    }
}

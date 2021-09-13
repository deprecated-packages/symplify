<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\LattePHPStanPrinter\LatteToPhpCompiler;
use Symplify\PHPStanRules\Nette\Latte\RelatedFileResolver\IncludedSnippetTemplateFileResolver;
use Symplify\PHPStanRules\Nette\Latte\RelatedFileResolver\ParentLayoutTemplateFileResolver;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\LatteVariableCollectingNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\ParentNodeAwarePhpParser;

final class LatteVariableNamesResolver
{
    public function __construct(
        private ParentNodeAwarePhpParser $parentNodeAwarePhpParser,
        private LatteToPhpCompiler $latteToPhpCompiler,
        private LatteVariableCollectingNodeVisitor $latteVariableCollectingNodeVisitor,
        private ParentLayoutTemplateFileResolver $parentLayoutTemplateFileResolver,
        private IncludedSnippetTemplateFileResolver $includedSnippetTemplateFileResolver
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $templateFilePath): array
    {
        $phpNodes = $this->parseTemplateFileNameToPhpNodes($templateFilePath);

        // resolve parent layout variables
        // 1. current template
        $templateFilePaths = [$templateFilePath];

        // 2. parent layout
        $parentLayoutFileName = $this->parentLayoutTemplateFileResolver->resolve($templateFilePath, $phpNodes);
        if ($parentLayoutFileName !== null) {
            $templateFilePaths[] = $parentLayoutFileName;
        }

        // 3. included templates
        $includedTemplateFilePaths = $this->includedSnippetTemplateFileResolver->resolve($templateFilePath, $phpNodes);
        $templateFilePaths = array_merge($templateFilePaths, $includedTemplateFilePaths);

        $usedVariableNames = [];
        foreach ($templateFilePaths as $templateFilePath) {
            $phpNodes = $this->parseTemplateFileNameToPhpNodes($templateFilePath);
            $currentUsedVariableNames = $this->resolveUsedVariableNamesFromPhpNodes($phpNodes);
            $usedVariableNames = array_merge($usedVariableNames, $currentUsedVariableNames);
        }

        return $usedVariableNames;
    }

    /**
     * @param Stmt[] $phpNodes
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
     * @return Stmt[]
     */
    private function parseTemplateFileNameToPhpNodes(string $templateFilePath): array
    {
        $parentLayoutCompiledPhp = $this->latteToPhpCompiler->compileFilePath($templateFilePath);
        return $this->parentNodeAwarePhpParser->parsePhpContent($parentLayoutCompiledPhp);
    }
}

<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler;

use PhpParser\Node\Stmt;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\LattePHPStanCompiler\PhpParser\NodeVisitor\TemplateVariableCollectingNodeVisitor;
use Symplify\LattePHPStanCompiler\RelatedFileResolver\IncludedSnippetTemplateFileResolver;
use Symplify\LattePHPStanCompiler\RelatedFileResolver\ParentLayoutTemplateFileResolver;
use Symplify\TemplatePHPStanCompiler\Contract\UsedVariableNamesResolverInterface;
use Symplify\TemplatePHPStanCompiler\PhpParser\ParentNodeAwarePhpParser;

final class LatteVariableNamesResolver implements UsedVariableNamesResolverInterface
{
    public function __construct(
        private ParentNodeAwarePhpParser $parentNodeAwarePhpParser,
        private LatteToPhpCompiler $latteToPhpCompiler,
        private ParentLayoutTemplateFileResolver $parentLayoutTemplateFileResolver,
        private IncludedSnippetTemplateFileResolver $includedSnippetTemplateFileResolver,
        private SimpleNameResolver $simpleNameResolver,
        private NodeFinder $nodeFinder,
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFilePath(string $filePath): array
    {
        $stmts = $this->parseTemplateFileNameToPhpNodes($filePath);

        // resolve parent layout variables
        // 1. current template
        $templateFilePaths = [$filePath];

        // 2. parent layout
        $parentLayoutFileName = $this->parentLayoutTemplateFileResolver->resolve($filePath, $stmts);
        if ($parentLayoutFileName !== null) {
            $templateFilePaths[] = $parentLayoutFileName;
        }

        // 3. included templates
        $includedTemplateFilePaths = $this->includedSnippetTemplateFileResolver->resolve($filePath, $stmts);
        $templateFilePaths = array_merge($templateFilePaths, $includedTemplateFilePaths);

        $usedVariableNames = [];
        foreach ($templateFilePaths as $templateFilePath) {
            $stmts = $this->parseTemplateFileNameToPhpNodes($templateFilePath);
            $currentUsedVariableNames = $this->resolveUsedVariableNamesFromPhpNodes($stmts);
            $usedVariableNames = array_merge($usedVariableNames, $currentUsedVariableNames);
        }

        return $usedVariableNames;
    }

    /**
     * @param Stmt[] $stmts
     * @return string[]
     */
    private function resolveUsedVariableNamesFromPhpNodes(array $stmts): array
    {
        $templateVariableCollectingNodeVisitor = new TemplateVariableCollectingNodeVisitor(
            ['this', 'iterations', 'ʟ_l', 'ʟ_v'],
            ['main'],
            $this->simpleNameResolver,
            $this->nodeFinder
        );

        $phpNodeTraverser = new NodeTraverser();
        $phpNodeTraverser->addVisitor($templateVariableCollectingNodeVisitor);
        $phpNodeTraverser->traverse($stmts);

        return $templateVariableCollectingNodeVisitor->getUsedVariableNames();
    }

    /**
     * @return Stmt[]
     */
    private function parseTemplateFileNameToPhpNodes(string $templateFilePath): array
    {
        $parentLayoutCompiledPhp = $this->latteToPhpCompiler->compileFilePath($templateFilePath, [], []);
        return $this->parentNodeAwarePhpParser->parsePhpContent($parentLayoutCompiledPhp);
    }
}

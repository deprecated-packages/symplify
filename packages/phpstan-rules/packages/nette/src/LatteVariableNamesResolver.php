<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Node\Stmt;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\LattePHPStanPrinter\LatteToPhpCompiler;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\VariableAndType;
use Symplify\PHPStanRules\Nette\Latte\RelatedFileResolver\IncludedSnippetTemplateFileResolver;
use Symplify\PHPStanRules\Nette\Latte\RelatedFileResolver\ParentLayoutTemplateFileResolver;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\TemplateVariableCollectingNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\ParentNodeAwarePhpParser;

final class LatteVariableNamesResolver
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
    public function resolveFromFile(string $templateFilePath): array
    {
        $stmts = $this->parseTemplateFileNameToPhpNodes($templateFilePath, []);

        // resolve parent layout variables
        // 1. current template
        $templateFilePaths = [$templateFilePath];

        // 2. parent layout
        $parentLayoutFileName = $this->parentLayoutTemplateFileResolver->resolve($templateFilePath, $stmts);
        if ($parentLayoutFileName !== null) {
            $templateFilePaths[] = $parentLayoutFileName;
        }

        // 3. included templates
        $includedTemplateFilePaths = $this->includedSnippetTemplateFileResolver->resolve($templateFilePath, $stmts);
        $templateFilePaths = array_merge($templateFilePaths, $includedTemplateFilePaths);

        $usedVariableNames = [];
        foreach ($templateFilePaths as $templateFilePath) {
            $stmts = $this->parseTemplateFileNameToPhpNodes($templateFilePath, []);
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
     * @param VariableAndType[] $variablesAndTypes
     * @return Stmt[]
     */
    private function parseTemplateFileNameToPhpNodes(string $templateFilePath, array $variablesAndTypes): array
    {
        $parentLayoutCompiledPhp = $this->latteToPhpCompiler->compileFilePath($templateFilePath, $variablesAndTypes);
        return $this->parentNodeAwarePhpParser->parsePhpContent($parentLayoutCompiledPhp);
    }
}

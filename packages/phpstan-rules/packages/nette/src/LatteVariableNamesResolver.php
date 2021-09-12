<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette;

use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\LattePHPStanPrinter\LatteToPhpCompiler;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\LatteVariableCollectingNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\ParentLayoutNameNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\NodeVisitor\TemplateIncludesNameNodeVisitor;
use Symplify\PHPStanRules\Nette\PhpParser\ParentNodeAwarePhpParser;

final class LatteVariableNamesResolver
{
    public function __construct(
        private ParentNodeAwarePhpParser $parentNodeAwarePhpParser,
        private LatteToPhpCompiler $latteToPhpCompiler,
        private LatteVariableCollectingNodeVisitor $latteVariableCollectingNodeVisitor,
        private ParentLayoutNameNodeVisitor $parentLayoutNameNodeVisitor,
        private TemplateIncludesNameNodeVisitor $templateIncludesNameNodeVisitor
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveFromFile(string $templateFilePath): array
    {
        $phpNodes = $this->parseTemplateFileNameToPhpNodes($templateFilePath);

        // resolve parent layout variables
        $parentUsedVariableNames = $this->resolveParentLayoutUsedVariableNames($templateFilePath, $phpNodes);

        // resolve included templates
        dump($templateFilePath);

        $templateIncludesNameNodeVisitor = $this->templateIncludesNameNodeVisitor;
        $templateIncludesNameNodeVisitor->setTemplateFilePath($templateFilePath);

        dump($phpNodes);
        die;

        $currentUsedVariableNames = $this->resolveUsedVariableNamesFromPhpNodes($phpNodes);

        return array_merge($parentUsedVariableNames, $currentUsedVariableNames);
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
     * @param Stmt[] $phpNodes
     */
    private function resolveParentFileNameFromPhpNodes(string $templateFilePath, array $phpNodes): ?string
    {
        $phpNodeTraverser = new NodeTraverser();
        $this->parentLayoutNameNodeVisitor->setTemplateFilePath($templateFilePath);

        $phpNodeTraverser->addVisitor($this->parentLayoutNameNodeVisitor);
        $phpNodeTraverser->traverse($phpNodes);

        return $this->parentLayoutNameNodeVisitor->getParentLayoutFileName();
    }

    /**
     * @param Stmt[] $phpNodes
     * @return string[]
     */
    private function resolveParentLayoutUsedVariableNames(string $templateFilePath, array $phpNodes): array
    {
        $parentLayoutFileName = $this->resolveParentFileNameFromPhpNodes($templateFilePath, $phpNodes);
        if ($parentLayoutFileName === null) {
            return [];
        }

        $parentLayoutPhpNodes = $this->parseTemplateFileNameToPhpNodes($parentLayoutFileName);
        return $this->resolveUsedVariableNamesFromPhpNodes($parentLayoutPhpNodes);
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

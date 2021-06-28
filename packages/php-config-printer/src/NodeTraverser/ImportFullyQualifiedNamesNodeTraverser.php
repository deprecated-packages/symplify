<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeTraverser;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Symplify\PhpConfigPrinter\NodeVisitor\ImportFullyQualifiedNamesNodeVisitor;
use Symplify\PhpConfigPrinter\Sorter\FullyQualifiedImportSorter;
use Symplify\PhpConfigPrinter\ValueObject\FullyQualifiedImport;
use Symplify\PhpConfigPrinter\ValueObject\ImportType;

final class ImportFullyQualifiedNamesNodeTraverser
{
    public function __construct(
        private ParentConnectingVisitor $parentConnectingVisitor,
        private ImportFullyQualifiedNamesNodeVisitor $importFullyQualifiedNamesNodeVisitor,
        private FullyQualifiedImportSorter $importSorter,
        private BuilderFactory $builderFactory
    ) {
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function traverseNodes(array $nodes): array
    {
        $this->collectNameImportsFromNodes($nodes);

        $imports = array_unique($this->importFullyQualifiedNamesNodeVisitor->getImports());

        return $this->addUseImportsToNamespace($nodes, $imports);
    }

    /**
     * @param Node[] $nodes
     * @param FullyQualifiedImport[] $imports
     * @return Node[]
     */
    private function addUseImportsToNamespace(array $nodes, array $imports): array
    {
        if ($imports === []) {
            return $nodes;
        }

        $imports = $this->importSorter->sortImports($imports);

        $useImports = $this->createUses($imports);

        return array_merge($useImports, [new Nop()], $nodes);
    }

    /**
     * @param Node[] $nodes
     */
    private function collectNameImportsFromNodes(array $nodes): void
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->parentConnectingVisitor);
        $nodeTraverser->addVisitor($this->importFullyQualifiedNamesNodeVisitor);
        $nodeTraverser->traverse($nodes);
    }

    /**
     * @param FullyQualifiedImport[] $imports
     * @return Use_[]
     */
    private function createUses(array $imports): array
    {
        $useImports = [];
        foreach ($imports as $import) {
            $name = new Name($import->getFullyQualified());

            $useBuilder = match ($import->getType()) {
                ImportType::FUNCTION_TYPE => $this->builderFactory->useFunction($name),
                ImportType::CONSTANT_TYPE => $this->builderFactory->useConst($name),
                default => $this->builderFactory->use($name),
            };

            $useImports[] = $useBuilder->getNode();
        }

        return $useImports;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeTraverser;

use Nette\Utils\Strings;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use Symplify\PhpConfigPrinter\NodeVisitor\ImportFullyQualifiedNamesNodeVisitor;

final class ImportFullyQualifiedNamesNodeTraverser
{
    /**
     * @var ImportFullyQualifiedNamesNodeVisitor
     */
    private $importFullyQualifiedNamesNodeVisitor;

    /**
     * @var BuilderFactory
     */
    private $builderFactory;

    public function __construct(
        ImportFullyQualifiedNamesNodeVisitor $importFullyQualifiedNamesNodeVisitor,
        BuilderFactory $builderFactory
    ) {
        $this->importFullyQualifiedNamesNodeVisitor = $importFullyQualifiedNamesNodeVisitor;
        $this->builderFactory = $builderFactory;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function traverseNodes(array $nodes): array
    {
        $nameImports = $this->collectNameImportsFromNodes($nodes);
        if ($nameImports === []) {
            return $nodes;
        }

        return $this->addUseImportsToNamespace($nodes, $nameImports);
    }

    /**
     * @param Node[] $nodes
     * @param string[] $nameImports
     * @return Node[]
     */
    private function addUseImportsToNamespace(array $nodes, array $nameImports): array
    {
        if ($nameImports === []) {
            return $nodes;
        }

        sort($nameImports);

        $useImports = $this->createUses($nameImports);
        $useImports[] = new Nop();

        return array_merge($useImports, $nodes);
    }

    /**
     * @param Node[] $nodes
     * @return string[]
     */
    private function collectNameImportsFromNodes(array $nodes): array
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($this->importFullyQualifiedNamesNodeVisitor);
        $nodeTraverser->traverse($nodes);

        $nameImports = $this->importFullyQualifiedNamesNodeVisitor->getNameImports();
        return array_unique($nameImports);
    }

    /**
     * @param string[] $nameImports
     * @return Use_[]
     */
    private function createUses(array $nameImports): array
    {
        $useImports = [];
        foreach ($nameImports as $nameImport) {
            $shortNameImport = Strings::after($nameImport, '\\', -1);

            if (function_exists($nameImport) || $shortNameImport === 'ref') {
                $useBuilder = $this->builderFactory->useFunction(new Name($nameImport));
                /** @var Use_ $use */
                $use = $useBuilder->getNode();
                $useImports[] = $use;
            } else {
                $useBuilder = $this->builderFactory->use(new Name($nameImport));
                /** @var Use_ $use */
                $use = $useBuilder->getNode();
                $useImports[] = $use;
            }
        }

        return $useImports;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;
use Symplify\PhpConfigPrinter\Naming\ClassNaming;

final class ImportFullyQualifiedNamesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string[]
     */
    private $nameImports = [];

    public function __construct(
        private ClassNaming $classNaming
    ) {
    }

    /**
     * @param Node[] $nodes
     * @return Node[]|null
     */
    public function beforeTraverse(array $nodes): ?array
    {
        $this->nameImports = [];

        return null;
    }

    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof FullyQualified) {
            return null;
        }

        $fullyQualifiedName = $node->toString();
        if (\str_starts_with($fullyQualifiedName, '\\')) {
            $fullyQualifiedName = ltrim($fullyQualifiedName, '\\');
        }

        if (! \str_contains($fullyQualifiedName, '\\')) {
            return new Name($fullyQualifiedName);
        }

        $shortClassName = $this->classNaming->getShortName($fullyQualifiedName);

        $this->nameImports[] = $fullyQualifiedName;

        return new Name($shortClassName);
    }

    /**
     * @return string[]
     */
    public function getNameImports(): array
    {
        return $this->nameImports;
    }
}

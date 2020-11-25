<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeVisitor;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;
use Symplify\PhpConfigPrinter\Naming\ClassNaming;

final class ImportFullyQualifiedNamesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var ClassNaming
     */
    private $classNaming;

    /**
     * @var string[]
     */
    private $nameImports = [];

    public function __construct(ClassNaming $classNaming)
    {
        $this->classNaming = $classNaming;
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

        // namespace-less class name
        if (Strings::startsWith($fullyQualifiedName, '\\')) {
            $fullyQualifiedName = ltrim($fullyQualifiedName, '\\');
        }

        if (! Strings::contains($fullyQualifiedName, '\\')) {
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

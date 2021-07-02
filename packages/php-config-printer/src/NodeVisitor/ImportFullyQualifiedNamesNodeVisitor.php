<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;
use Symplify\PhpConfigPrinter\Naming\ClassNaming;
use Symplify\PhpConfigPrinter\ValueObject\AttributeKey;
use Symplify\PhpConfigPrinter\ValueObject\FullyQualifiedImport;
use Symplify\PhpConfigPrinter\ValueObject\ImportType;

final class ImportFullyQualifiedNamesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var FullyQualifiedImport[]
     */
    private $imports = [];

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
        $this->imports = [];

        return null;
    }

    public function enterNode(Node $node): ?Node
    {
        if (! $node instanceof FullyQualified) {
            return null;
        }

        $parent = $node->getAttribute(AttributeKey::PARENT);

        $fullyQualifiedName = $node->toString();
        if (\str_starts_with($fullyQualifiedName, '\\')) {
            $fullyQualifiedName = ltrim($fullyQualifiedName, '\\');
        }

        if (! \str_contains($fullyQualifiedName, '\\')) {
            return new Name($fullyQualifiedName);
        }

        $shortClassName = $this->classNaming->getShortName($fullyQualifiedName);

        if ($parent instanceof FuncCall) {
            $import = new FullyQualifiedImport(ImportType::FUNCTION_TYPE, $fullyQualifiedName);
        } else {
            $import = new FullyQualifiedImport(ImportType::CLASS_TYPE, $fullyQualifiedName);
        }

        $this->imports[] = $import;

        return new Name($shortClassName);
    }

    /**
     * @return FullyQualifiedImport[]
     */
    public function getImports(): array
    {
        return $this->imports;
    }
}

<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeVisitorAbstract;
use Symplify\Astral\Naming\SimpleNameResolver;

final class CollectForeachedVariablesNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<string, string>
     */
    private array $foreachedVariablesToSingles = [];

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
    ) {
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function beforeTraverse(array $nodes): array
    {
        $this->foreachedVariablesToSingles = [];
        return $nodes;
    }

    public function enterNode(Node $node): Node|null
    {
        if (! $node instanceof Foreach_) {
            return null;
        }

        if (! $node->expr instanceof Variable) {
            return null;
        }

        if (! $node->valueVar instanceof Variable) {
            return null;
        }

        $foreachedVariable = $this->simpleNameResolver->getName($node->expr);
        if ($foreachedVariable === null) {
            return null;
        }

        $singleVariable = $this->simpleNameResolver->getName($node->valueVar);
        if ($singleVariable === null) {
            return null;
        }

        $this->foreachedVariablesToSingles[$foreachedVariable] = $singleVariable;

        return null;
    }

    /**
     * @return array<string, string>
     */
    public function getForeachedVariablesToSingles(): array
    {
        return $this->foreachedVariablesToSingles;
    }
}

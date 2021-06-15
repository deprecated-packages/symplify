<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\ValueObject\AttributeKey;

final class StatementFinder
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private NodeFinder $nodeFinder
    ) {
    }

    public function isUsedInNextStatement(Assign $assign, Node $node): bool
    {
        $var = $assign->var;
        $varClass = $var::class;
        $next = $node->getAttribute(AttributeKey::NEXT);
        $parentOfParentAssignment = $node->getAttribute(AttributeKey::PARENT);

        while ($next) {
            $nextVars = $this->nodeFinder->findInstanceOf($next, $varClass);
            if ($this->hasSameVar($nextVars, $parentOfParentAssignment, $var)) {
                return true;
            }

            $next = $next->getAttribute(AttributeKey::NEXT);
        }

        return false;
    }

    /**
     * @param Node[] $nodes
     */
    private function hasSameVar(array $nodes, Node $parentOfParentAssignNode, Expr $varExpr): bool
    {
        foreach ($nodes as $node) {
            $parent = $node->getAttribute(AttributeKey::PARENT);
            $parentOfParentNode = $parent->getAttribute(AttributeKey::PARENT);

            if (! $this->simpleNameResolver->areNamesEqual($node, $varExpr)) {
                continue;
            }

            if ($parentOfParentNode !== $parentOfParentAssignNode) {
                return true;
            }
        }

        return false;
    }
}

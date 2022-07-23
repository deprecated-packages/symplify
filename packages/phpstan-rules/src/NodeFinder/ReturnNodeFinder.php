<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use Symplify\Astral\NodeTraverser\SimpleCallableNodeTraverser;

final class ReturnNodeFinder
{
    public function __construct(
        private SimpleCallableNodeTraverser $simpleCallableNodeTraverser
    ) {
    }

    public function findOnlyReturnsExpr(ClassMethod $classMethod): Expr|null
    {
        $returns = $this->findReturnsWithValues($classMethod);
        if (count($returns) !== 1) {
            return null;
        }

        $onlyReturn = $returns[0];
        return $onlyReturn->expr;
    }

    /**
     * @return Return_[]
     */
    public function findReturnsWithValues(ClassMethod $classMethod): array
    {
        $returns = [];

        $this->simpleCallableNodeTraverser->traverseNodesWithCallable((array) $classMethod->stmts, static function (
            Node $node
        ) use (&$returns) {
            // skip different scope
            if ($node instanceof FunctionLike) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }
            if (! $node instanceof Return_) {
                return null;
            }
            if ($node->expr === null) {
                return null;
            }
            $returns[] = $node;
        });

        return $returns;
    }
}

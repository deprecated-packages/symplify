<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanRules\Printer\NodeComparator;

final class MethodCallNodeFinder
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
        private NodeFinder $nodeFinder,
        private NodeComparator $nodeComparator,
    ) {
    }

    /**
     * @return MethodCall[]
     */
    public function findUsages(MethodCall $methodCall): array
    {
        $class = $this->simpleNodeFinder->findFirstParentByType($methodCall, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        return $this->nodeFinder->find($class, function (Node $node) use ($methodCall): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            if (! $this->nodeComparator->areNodesEqual($node->var, $methodCall->var)) {
                return false;
            }

            return $this->nodeComparator->areNodesEqual($node->name, $methodCall->name);
        });
    }
}

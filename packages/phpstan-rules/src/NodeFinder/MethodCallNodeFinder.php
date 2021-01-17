<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use Symplify\PHPStanRules\Printer\NodeComparator;

final class MethodCallNodeFinder
{
    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    public function __construct(
        ParentNodeFinder $parentNodeFinder,
        NodeFinder $nodeFinder,
        NodeComparator $nodeComparator
    ) {
        $this->parentNodeFinder = $parentNodeFinder;
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
    }

    /**
     * @return MethodCall[]
     */
    public function findUsages(MethodCall $methodCall): array
    {
        $class = $this->parentNodeFinder->getFirstParentByType($methodCall, Class_::class);
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

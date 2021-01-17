<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
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

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(
        ParentNodeFinder $parentNodeFinder,
        NodeFinder $nodeFinder,
        NodeComparator $nodeComparator,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->parentNodeFinder = $parentNodeFinder;
        $this->nodeFinder = $nodeFinder;
        $this->nodeComparator = $nodeComparator;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return MethodCall[]
     */
    public function findByName(Node $node, string $methodName): array
    {
        return $this->nodeFinder->find([$node], function (Node $node) use ($methodName): bool {
            if (! $node instanceof MethodCall) {
                return false;
            }

            return $this->simpleNameResolver->isName($node->name, $methodName);
        });
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

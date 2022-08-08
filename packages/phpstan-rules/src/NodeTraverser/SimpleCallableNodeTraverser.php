<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeTraverser;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use Symplify\PHPStanRules\NodeVisitor\CallableNodeVisitor;

/**
 * @api
 */
final class SimpleCallableNodeTraverser
{
    /**
     * @param callable(Node $node): (int|Node|null) $callable
     * @param Node|Node[]|null $nodes
     */
    public function traverseNodesWithCallable(Node | array | null $nodes, callable $callable): void
    {
        if ($nodes === null) {
            return;
        }

        if ($nodes === []) {
            return;
        }

        if (! is_array($nodes)) {
            $nodes = [$nodes];
        }

        $nodeTraverser = new NodeTraverser();
        $callableNodeVisitor = new CallableNodeVisitor($callable);
        $nodeTraverser->addVisitor($callableNodeVisitor);
        $nodeTraverser->traverse($nodes);
    }
}

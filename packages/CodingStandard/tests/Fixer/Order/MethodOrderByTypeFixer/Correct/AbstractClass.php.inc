<?php declare(strict_types=1);

namespace RandomNamespace;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Rector\Builder\ExpressionAdder;
use Rector\Builder\PropertyAdder;
use Rector\Contract\Rector\PhpRectorInterface;

abstract class SomeRectorClass extends NodeVisitorAbstract implements PhpRectorInterface
{
    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    final public function beforeTraverse(array $nodes): array
    {
        return $nodes;
    }

    /**
     * @return int|Node|null
     */
    final public function enterNode(Node $node)
    {
        $nodeClass = get_class($node);
        if (! $this->isMatchingNodeType($nodeClass)) {
            return null;
        }

        $newNode = $this->refactor($node);
        if ($newNode !== null) {
            return $newNode;
        }

        if ($this->removeNode) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        return null;
    }

    /**
     * @return bool|int|Node
     */
    public function leaveNode(Node $node)
    {
        if ($this->removeNode) {
            $this->removeNode = false;
            return NodeTraverser::REMOVE_NODE;
        }

        return $node;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    public function afterTraverse(array $nodes): array
    {
        $nodes = $this->expressionAdder->addExpressionsToNodes($nodes);
        return $this->propertyAdder->addPropertiesToNodes($nodes);
    }
}

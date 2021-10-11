<?php

declare(strict_types=1);

namespace Symplify\EasyCI\ActiveClass\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

final class ClassNameNodeVisitor extends NodeVisitorAbstract
{
    private string|null $className = null;

    public function beforeTraverse(array $nodes)
    {
        $this->className = null;
        return $nodes;
    }

    public function enterNode(Node $node)
    {
        if (! $node instanceof ClassLike) {
            return null;
        }

        if ($node->name === null) {
            return null;
        }

        $this->className = $node->namespacedName->toString();

        return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }
}
